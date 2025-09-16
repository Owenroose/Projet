<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider les tables de permissions et de rôles
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer toutes les permissions basées sur vos contrôleurs
        $permissions = [
            'read-role', 'create-role', 'update-role', 'delete-role',
            'read-user', 'create-user', 'update-user', 'delete-user',
            'read-project', 'create-project', 'update-project', 'delete-project',
            'read-service', 'create-service', 'update-service', 'delete-service', 'toggle-service-published',
            'read-product', 'create-product', 'update-product', 'delete-product',
            'read-testimonial', 'create-testimonial', 'update-testimonial', 'delete-testimonial',
            'read-team-member', 'create-team-member', 'update-team-member', 'delete-team-member',
            'read-contact', 'update-contact', 'delete-contact', 'send-contact-response', 'mark-contact-read', 'mark-contact-unread', 'bulk-contact-action',
            'view-profile', 'update-profile',
            'use-ai-assistant', 'view-ai-stats',
            'read-order', 'create-order', 'update-order', 'delete-order', 'update-order-status', 'assign-driver', 'export-orders', 'view-order-dashboard',
            'read-delivery', 'validate-delivery', 'manage-delivery',
            'view-admin-dashboard', 'view-dashboard',
            'access-admin-panel',
        ];

        // Créer les permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $contentManagerRole = Role::firstOrCreate(['name' => 'Content Manager']);
        $customerServiceRole = Role::firstOrCreate(['name' => 'Customer Service']);
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $driverRole = Role::firstOrCreate(['name' => 'Livreur']);

        // Assigner toutes les permissions au Super Admin
        $superAdminRole->syncPermissions(Permission::all());

        // Permissions pour les autres rôles
        $adminPermissions = [
            'access-admin-panel', 'view-admin-dashboard', 'view-dashboard',
            'read-project', 'create-project', 'update-project', 'delete-project',
            'read-service', 'create-service', 'update-service', 'delete-service', 'toggle-service-published',
            'read-product', 'create-product', 'update-product', 'delete-product',
            'read-testimonial', 'create-testimonial', 'update-testimonial', 'delete-testimonial',
            'read-team-member', 'create-team-member', 'update-team-member', 'delete-team-member',
            'read-contact', 'update-contact', 'delete-contact', 'send-contact-response', 'mark-contact-read', 'mark-contact-unread', 'bulk-contact-action',
            'read-order', 'update-order', 'delete-order', 'update-order-status', 'assign-driver', 'export-orders', 'view-order-dashboard',
            'read-delivery', 'manage-delivery',
            'view-profile', 'update-profile',
            'use-ai-assistant', 'view-ai-stats',
        ];
        $adminRole->syncPermissions($adminPermissions);

        $contentManagerPermissions = [
            'access-admin-panel', 'view-admin-dashboard', 'view-dashboard',
            'read-project', 'create-project', 'update-project', 'delete-project',
            'read-service', 'create-service', 'update-service', 'delete-service', 'toggle-service-published',
            'read-product', 'create-product', 'update-product', 'delete-product',
            'read-testimonial', 'create-testimonial', 'update-testimonial', 'delete-testimonial',
            'read-team-member', 'create-team-member', 'update-team-member', 'delete-team-member',
            'view-profile', 'update-profile',
            'use-ai-assistant', 'view-ai-stats',
        ];
        $contentManagerRole->syncPermissions($contentManagerPermissions);

        $customerServicePermissions = [
            'access-admin-panel', 'view-admin-dashboard', 'view-dashboard',
            'read-contact', 'update-contact', 'send-contact-response', 'mark-contact-read', 'mark-contact-unread', 'bulk-contact-action',
            'read-project', 'read-service', 'read-product', 'read-testimonial', 'read-team-member', 'read-order',
            'view-profile', 'update-profile',
            'use-ai-assistant',
        ];
        $customerServiceRole->syncPermissions($customerServicePermissions);

        $driverPermissions = [
            'read-delivery', 'validate-delivery',
            'view-dashboard',
            'view-profile', 'update-profile',
        ];
        $driverRole->syncPermissions($driverPermissions);

        $userPermissions = [
            'view-dashboard',
            'view-profile', 'update-profile',
        ];
        $userRole->syncPermissions($userPermissions);

        // Assigner le rôle "Super Admin" au premier utilisateur
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->syncRoles(['Super Admin']);
            $this->command->info('Le rôle "Super Admin" a été assigné à l\'utilisateur: ' . $firstUser->name . ' (' . $firstUser->email . ')');
        } else {
            $this->command->warn('Aucun utilisateur trouvé dans la base de données. Assurez-vous d\'avoir créé un utilisateur avant de lancer le seeder.');
        }

        $this->command->info('Permissions et rôles mis à jour et assignés avec succès !');
    }
}
