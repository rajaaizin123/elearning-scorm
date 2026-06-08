<?php

namespace Database\Seeders;

use App\Domain\Academic\ClassGroup;
use App\Domain\Academic\Course;
use App\Domain\Academic\Enrollment;
use App\Domain\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = User::query()->where('email', 'dosen@example.com')->first();
        $mahasiswa = User::query()->where('email', 'mahasiswa@example.com')->first();

        if (!$dosen || !$mahasiswa) {
            return;
        }

        // Create test courses
        $courses = [
            [
                'semester' => '2025-2',
                'code' => 'IF201',
                'title' => 'Pemrograman Web',
                'description' => 'Belajar dasar-dasar pemrograman web dengan Laravel dan JavaScript modern.',
                'credit' => 3,
                'status' => 'published',
            ],
            [
                'semester' => '2025-2',
                'code' => 'IF202',
                'title' => 'Basis Data Lanjut',
                'description' => 'Teknik database design, normalisasi, dan query optimization untuk aplikasi skala besar.',
                'credit' => 3,
                'status' => 'published',
            ],
            [
                'semester' => '2025-2',
                'code' => 'IF203',
                'title' => 'Algoritma & Struktur Data',
                'description' => 'Algoritma fundamental dan struktur data untuk problem solving yang efisien.',
                'credit' => 4,
                'status' => 'published',
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::query()->firstOrCreate(
                ['code' => $courseData['code']],
                [
                    ...$courseData,
                    'lecturer_id' => $dosen->id,
                ]
            );

            // Create class group for each course
            $classGroup = ClassGroup::query()->firstOrCreate(
                ['course_id' => $course->id, 'name' => 'Kelas A'],
                [
                    'lecturer_id' => $dosen->id,
                    'capacity' => 40,
                    'status' => 'active',
                ]
            );

            // Enroll mahasiswa
            Enrollment::query()->firstOrCreate(
                ['class_group_id' => $classGroup->id, 'user_id' => $mahasiswa->id],
                ['status' => 'active', 'enrolled_at' => now()]
            );
        }
    }
}
