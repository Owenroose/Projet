<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer toutes les permissions basées sur vos contrôleurs
        $permissions = [
            // Permissions pour les rôles (AdminRoleController)
            'read-role',
            'create-role',
            'update-role',
            'delete-role',

            // Permissions pour les utilisateurs (AdminUserController)
            'read-user',
            'create-user',
            'update-user',
            'delete-user',

            // Permissions pour les projets (AdminProjectController)
            'read-project',
            'create-project',
            'update-project',
            'delete-project',

            // Permissions pour les services (AdminServiceController)
            'read-service',
            'create-service',
            'update-service',
            'delete-service',
            'toggle-service-published',

            // Permissions pour les produits (AdminProductController)
            'read-product',
            'create-product',
            'update-product',
            'delete-product',

            // Permissions pour les témoignages (AdminTestimonialController)
            'read-testimonial',
            'create-testimonial',
            'update-testimonial',
            'delete-testimonial',

            // Permissions pour l'équipe (AdminTeamMemberController)
            'read-team-member',
            'create-team-member',
            'update-team-member',
            'delete-team-member',

            // Permissions pour les contacts (AdminContactController)
            'read-contact',
            'update-contact',
            'delete-contact',
            'send-contact-response',
            'mark-contact-read',
            'mark-contact-unread',
            'bulk-contact-action',

            // Permissions pour le profil (AdminProfileController)
            'view-profile',
            'update-profile',

            // Permissions pour l'assistant AI (AiAssistantController)
            'use-ai-assistant',
            'view-ai-stats',

            // Permissions pour le tableau de bord
            'view-admin-dashboard',
            'view-dashboard',

            // Permissions générales
            'access-admin-panel',
        ];

        // Créer les permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $adminRole = Role::create(['name' => 'Admin']);
        $contentManagerRole = Role::create(['name' => 'Content Manager']);
        $customerServiceRole = Role::create(['name' => 'Customer Service']);
        $userRole = Role::create(['name' => 'User']);

        // Assigner toutes les permissions au Super Admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Permissions pour l'Admin (presque tout sauf gestion des rôles et utilisateurs)
        $adminPermissions = [
            'access-admin-panel',
            'view-admin-dashboard',
            'view-dashboard',

            // Projets
            'read-project',
            'create-project',
            'update-project',
            'delete-project',

            // Services
            'read-service',
            'create-service',
            'update-service',
            'delete-service',
            'toggle-service-published',

            // Produits
            'read-product',
            'create-product',
            'update-product',
            'delete-product',

            // Témoignages
            'read-testimonial',
            'create-testimonial',
            'update-testimonial',
            'delete-testimonial',

            // Équipe
            'read-team-member',
            'create-team-member',
            'update-team-member',
            'delete-team-member',

            // Contacts
            'read-contact',
            'update-contact',
            'delete-contact',
            'send-contact-response',
            'mark-contact-read',
            'mark-contact-unread',
            'bulk-contact-action',

            // Profil
            'view-profile',
            'update-profile',

            // AI Assistant
            'use-ai-assistant',
            'view-ai-stats',
        ];
        $adminRole->givePermissionTo($adminPermissions);

        // Permissions pour le Content Manager (gestion du contenu)
        $contentManagerPermissions = [
            'access-admin-panel',
            'view-admin-dashboard',
            'view-dashboard',

            // Projets
            'read-project',
            'create-project',
            'update-project',
            'delete-project',

            // Services
            'read-service',
            'create-service',
            'update-service',
            'delete-service',
            'toggle-service-published',

            // Produits
            'read-product',
            'create-product',
            'update-product',
            'delete-product',

            // Témoignages
            'read-testimonial',
            'create-testimonial',
            'update-testimonial',
            'delete-testimonial',

            // Équipe
            'read-team-member',
            'create-team-member',
            'update-team-member',
            'delete-team-member',

            // Profil
            'view-profile',
            'update-profile',

            // AI Assistant
            'use-ai-assistant',
            'view-ai-stats',
        ];
        $contentManagerRole->givePermissionTo($contentManagerPermissions);

        // Permissions pour Customer Service (gestion des contacts principalement)
        $customerServicePermissions = [
            'access-admin-panel',
            'view-admin-dashboard',
            'view-dashboard',

            // Contacts
            'read-contact',
            'update-contact',
            'send-contact-response',
            'mark-contact-read',
            'mark-contact-unread',
            'bulk-contact-action',

            // Lecture seule pour le reste
            'read-project',
            'read-service',
            'read-product',
            'read-testimonial',
            'read-team-member',

            // Profil
            'view-profile',
            'update-profile',

            // AI Assistant
            'use-ai-assistant',
        ];
        $customerServiceRole->givePermissionTo($customerServicePermissions);

        // Permissions pour l'User basique (accès minimal)
        $userPermissions = [
            'view-dashboard',
            'view-profile',
            'update-profile',
        ];
        $userRole->givePermissionTo($userPermissions);

        $this->command->info('Permissions et rôles créés avec succès !');
        $this->command->info('Rôles créés : Super Admin, Admin, Content Manager, Customer Service, User');
        $this->command->info('Total permissions : ' . count($permissions));
        $this->command->info('Permissions basées sur vos contrôleurs existants dans web.php');
    }
}
