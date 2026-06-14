<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_adjacent_schedule_is_allowed_for_the_same_teacher_and_class(): void
    {
        [$admin, $guru] = $this->adminAndTeacher();
        $this->createSchedule($guru, '07:00', '08:00');

        $this->actingAs($admin)
            ->post(route('admin.jadwal.store'), $this->schedulePayload($guru, [
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:00',
            ]))
            ->assertRedirect(route('admin.jadwal'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('jadwal', 2);
    }

    public function test_overlapping_schedule_is_rejected(): void
    {
        [$admin, $guru] = $this->adminAndTeacher();
        $this->createSchedule($guru, '07:00', '08:00');

        $this->actingAs($admin)
            ->from(route('admin.jadwal'))
            ->post(route('admin.jadwal.store'), $this->schedulePayload($guru, [
                'jam_mulai' => '07:30',
                'jam_selesai' => '08:30',
            ]))
            ->assertRedirect(route('admin.jadwal'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('jadwal', 1);
    }

    public function test_empty_rooms_do_not_conflict_when_teacher_and_class_are_different(): void
    {
        [$admin, $firstTeacher] = $this->adminAndTeacher();
        $secondTeacher = Guru::create(['nama' => 'Guru Kedua', 'mapel' => 'IPA']);
        $this->createSchedule($firstTeacher, '07:00', '08:00', '1A', null);

        $this->actingAs($admin)
            ->post(route('admin.jadwal.store'), $this->schedulePayload($secondTeacher, [
                'kelas' => '2A',
                'ruangan' => null,
            ]))
            ->assertRedirect(route('admin.jadwal'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('jadwal', 2);
    }

    private function adminAndTeacher(): array
    {
        return [
            User::create([
                'username' => 'admin-jadwal',
                'password' => 'secret-password',
                'role' => 'admin',
            ]),
            Guru::create(['nama' => 'Guru Pertama', 'mapel' => 'Matematika']),
        ];
    }

    private function createSchedule(
        Guru $guru,
        string $start,
        string $end,
        string $class = '1A',
        ?string $room = 'Ruang 1A'
    ): Jadwal {
        return Jadwal::create($this->schedulePayload($guru, [
            'jam_mulai' => $start,
            'jam_selesai' => $end,
            'kelas' => $class,
            'ruangan' => $room,
        ]));
    }

    private function schedulePayload(Guru $guru, array $overrides = []): array
    {
        return array_merge([
            'hari' => 'Senin',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:00',
            'mapel' => 'Matematika',
            'id_guru' => $guru->id,
            'kelas' => '1A',
            'ruangan' => 'Ruang 1A',
        ], $overrides);
    }
}
