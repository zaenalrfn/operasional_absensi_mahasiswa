<?php
// database/seeders/RolePermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Lectures;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==================== CREATE PERMISSIONS ====================
        $permissions = [
            // Attendance Permissions
            'attendance.view_any',           // View all attendances (admin/dosen)
            'attendance.view_own',           // View own attendance (mahasiswa)
            'attendance.create',             // Create attendance (mahasiswa)
            'attendance.update',             // Update attendance (admin/dosen)
            'attendance.verify',             // Verify attendance (dosen)
            'attendance.delete',             // Delete attendance (admin)

            // Course Permissions
            'course.view_any',               // View all courses
            'course.view_own',               // View own courses
            'course.create',                 // Create courses (admin/dosen)
            'course.update',                 // Update courses (admin/dosen)
            'course.delete',                 // Delete courses (admin)

            // User Management Permissions
            'user.view_any',                 // View all users (admin)
            'user.view_own',                 // View own profile
            'user.create',                   // Create users (admin)
            'user.update',                   // Update users (admin)
            'user.delete',                   // Delete users (admin)
            'user.manage_roles',             // Manage roles (admin)

            // Lecturer Permissions
            'lecturer.view_any',             // View all lecturers
            'lecturer.create',               // Create lecturers (admin)
            'lecturer.update',               // Update lecturers (admin)
            'lecturer.delete',               // Delete lecturers (admin)

            // Student Management Permissions
            'student.view_any',              // View all students (admin/dosen)
            'student.create',                // Create students (admin)
            'student.update',                // Update students (admin)
            'student.delete',                // Delete students (admin)

            // Student Course Permissions (penting!)
            'student_course.view_own',
            'student_course.create',
            'student_course.update',
            'student_course.delete',

            // Report Permissions
            'report.view_attendance',        // View attendance reports
            'report.view_academic',          // View academic reports
            'report.export',                 // Export reports

            // System Permissions
            'system.settings',               // Manage system settings (admin)
            'system.backup',                 // Backup system (admin)
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ==================== CREATE ROLES ====================

        // Super Admin Role - All permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // Admin Role - Most administrative permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminPermissions = [
            'attendance.view_any',
            'attendance.update',
            'attendance.delete',

            'course.view_any',
            'course.create',
            'course.update',
            'course.delete',

            'user.view_any',
            'user.create',
            'user.update',
            'user.delete',
            'user.manage_roles',

            'lecturer.view_any',
            'lecturer.create',
            'lecturer.update',
            'lecturer.delete',

            'student.view_any',
            'student.create',
            'student.update',
            'student.delete',

            'report.view_attendance',
            'report.view_academic',
            'report.export',

            'system.settings',
            'system.backup',
        ];
        $adminRole->syncPermissions($adminPermissions);


        // Mahasiswa Role - Student permissions
        $mahasiswaRole = Role::firstOrCreate(['name' => 'mahasiswa', 'guard_name' => 'web']);
        $mahasiswaPermissions = [
            'attendance.view_own',
            'attendance.create',

            'course.view_own',
            'student_course.view_own',
            'student_course.create',

            'user.view_own',
            'user.update',
        ];
        $mahasiswaRole->syncPermissions($mahasiswaPermissions);

        // Dosen Role - Lecturer permissions
        $dosenRole = Role::firstOrCreate(['name' => 'dosen', 'guard_name' => 'web']);
        $dosenPermissions = [
            'attendance.view_any',
            'attendance.update',
            'attendance.verify',
            'lecturer.view_any',
            'course.view_any',
            'student.view_any',
        ];
        $dosenRole->syncPermissions($dosenPermissions);

        // ==================== CREATE DEFAULT USERS ====================

        $this->createDefaultUsers();
    }

    private function createDefaultUsers()
    {
        // Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@university.ac.id'],
            [
                'id' => 1,
                'nim' => 'SUPER001',
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'program_studi' => 'Teknik Informatika',
                'semester' => 1,
                'kelas' => 'A',
                'photo_url' => null,
            ]
        );
        $superAdmin->assignRole('super-admin');

        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@university.ac.id'],
            [
                'id' => 2,
                'nim' => 'ADMIN001',
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'program_studi' => 'Sistem Informasi',
                'semester' => 1,
                'kelas' => 'A',
                'photo_url' => null,
            ]
        );
        $admin->assignRole('admin');

        // Dosen Users (legacy lectures table)
        Lectures::firstOrCreate(
            ['email' => 'ahmad.santoso@university.ac.id'],
            [
                'id' => 1,
                'name' => 'Prof. Dr. Ahmad Santoso, M.Kom.',
            ]
        );

        Lectures::firstOrCreate(
            ['email' => 'siti.rahayu@university.ac.id'],
            [
                'id' => 2,
                'name' => 'Dr. Siti Rahayu, M.T.',
            ]
        );

        Lectures::firstOrCreate(
            ['email' => 'dewi.anggraeni@university.ac.id'],
            [
                'id' => 3,
                'name' => 'Dewi Anggraeni, M.Kom.',
            ]
        );

        // Dosen Users (agar bisa login)
        $dosen1 = User::firstOrCreate(
            ['email' => 'ahmad.santoso@university.ac.id'],
            [
                'id' => 20, // ID distinct
                'nim' => 'DOSEN001',
                'name' => 'Prof. Dr. Ahmad Santoso, M.Kom.',
                'password' => Hash::make('password123'),
                'program_studi' => 'Teknik Informatika',
                'semester' => 0,
                'kelas' => '-',
                'photo_url' => null,
            ]
        );
        $dosen1->assignRole('dosen');

        $dosen2 = User::firstOrCreate(
            ['email' => 'siti.rahayu@university.ac.id'],
            [
                'id' => 21,
                'nim' => 'DOSEN002',
                'name' => 'Dr. Siti Rahayu, M.T.',
                'password' => Hash::make('password123'),
                'program_studi' => 'Sistem Informasi',
                'semester' => 0,
                'kelas' => '-',
                'photo_url' => null,
            ]
        );
        $dosen2->assignRole('dosen');

        // Sample Mahasiswa Users
        $mahasiswa1 = User::firstOrCreate(
            ['email' => 'budi.santoso@university.ac.id'],
            [
                'id' => 4,
                'nim' => '202101001',
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'program_studi' => 'Teknik Informatika',
                'semester' => 3,
                'kelas' => 'TI-3A',
                'photo_url' => null,
            ]
        );
        $mahasiswa1->assignRole('mahasiswa');

        $mahasiswa2 = User::firstOrCreate(
            ['email' => 'sari.indah@university.ac.id'],
            [
                'id' => 5,
                'nim' => '202101002',
                'name' => 'Sari Indah',
                'password' => Hash::make('password123'),
                'program_studi' => 'Sistem Informasi',
                'semester' => 3,
                'kelas' => 'SI-3B',
                'photo_url' => null,
            ]
        );
        $mahasiswa2->assignRole('mahasiswa');

        $mahasiswa3 = User::firstOrCreate(
            ['email' => 'rizki.pratama@university.ac.id'],
            [
                'id' => 6,
                'nim' => '202101003',
                'name' => 'Rizki Pratama',
                'password' => Hash::make('password123'),
                'program_studi' => 'Teknik Komputer',
                'semester' => 5,
                'kelas' => 'TK-5A',
                'photo_url' => null,
            ]
        );
        $mahasiswa3->assignRole('mahasiswa');

        $mahasiswa4 = User::firstOrCreate(
            ['email' => 'maya.sari@university.ac.id'],
            [
                'id' => 7,
                'nim' => '202101004',
                'name' => 'Maya Sari',
                'password' => Hash::make('password123'),
                'program_studi' => 'Teknik Informatika',
                'semester' => 3,
                'kelas' => 'TI-3B',
                'photo_url' => null,
            ]
        );
        $mahasiswa4->assignRole('mahasiswa');

        $mahasiswa5 = User::firstOrCreate(
            ['email' => 'ari.wibowo@university.ac.id'],
            [
                'id' => 8,
                'nim' => '202101005',
                'name' => 'Ari Wibowo',
                'password' => Hash::make('password123'),
                'program_studi' => 'Sistem Informasi',
                'semester' => 5,
                'kelas' => 'SI-5A',
                'photo_url' => null,
            ]
        );
        $mahasiswa5->assignRole('mahasiswa');

        $mahasiswa6 = User::firstOrCreate(
            ['email' => 'ulfahnafiah@university.ac.id'],
            [
                'id' => 9,
                'nim' => '5231811026',
                'name' => 'Ulfah Nafiah',
                'password' => Hash::make('5231811026_Ulfah'),
                'program_studi' => 'Sains Data',
                'semester' => 5,
                'kelas' => 'A',
                'photo_url' => null,
            ]
        );
        $mahasiswa6->assignRole('mahasiswa');

        $mahasiswa7 = User::firstOrCreate(
            ['email' => 'dianekapratiwi@university.ac.id'],
            [
                'id' => 10,
                'nim' => '5231811014',
                'name' => 'Dian Eka Pratiwi',
                'password' => Hash::make('5231811014_Dian'),
                'program_studi' => 'Sains Data',
                'semester' => 5,
                'kelas' => 'A',
                'photo_url' => null,
            ]
        );
        $mahasiswa7->assignRole('mahasiswa');

        $mahasiswa8 = User::firstOrCreate(
            ['email' => 'yogihanusanjaya@university.ac.id'],
            [
                'id' => 11,
                'nim' => '5231811035',
                'name' => 'Yogi Hanusanjaya',
                'password' => Hash::make('5231811035_Yogi'),
                'program_studi' => 'Sains Data',
                'semester' => 5,
                'kelas' => 'A',
                'photo_url' => null,
            ]
        );
        $mahasiswa8->assignRole('mahasiswa');

        $mahasiswa8 = User::firstOrCreate(
            ['email' => 'sophiafebyiena@university.ac.id'],
            [
                'id' => 12,
                'nim' => '5231811008',
                'name' => 'Sophia Febyiena M',
                'password' => Hash::make('5231811008_Sophia'),
                'program_studi' => 'Sains Data',
                'semester' => 5,
                'kelas' => 'A',
                'photo_url' => null,
            ]
        );
        $mahasiswa8->assignRole('mahasiswa');

        $this->command->info('Default roles, permissions, and users created successfully!');
        $this->command->info('Super Admin: superadmin@university.ac.id / password123');
        $this->command->info('Admin: admin@university.ac.id / password123');
        $this->command->info('Dosen: ahmad.santoso@university.ac.id / password123');
        $this->command->info('Mahasiswa: budi.santoso@university.ac.id / password123');
    }
}
