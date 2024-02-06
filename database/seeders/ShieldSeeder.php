<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"admin","permissions":["view_admin","view_any_admin","create_admin","update_admin","restore_admin","restore_any_admin","replicate_admin","reorder_admin","delete_admin","delete_any_admin","force_delete_admin","force_delete_any_admin","view_attribute","view_any_attribute","create_attribute","update_attribute","restore_attribute","restore_any_attribute","replicate_attribute","reorder_attribute","delete_attribute","delete_any_attribute","force_delete_attribute","force_delete_any_attribute","view_brand","view_any_brand","create_brand","update_brand","restore_brand","restore_any_brand","replicate_brand","reorder_brand","delete_brand","delete_any_brand","force_delete_brand","force_delete_any_brand","view_category","view_any_category","create_category","update_category","restore_category","restore_any_category","replicate_category","reorder_category","delete_category","delete_any_category","force_delete_category","force_delete_any_category","view_currency","view_any_currency","create_currency","update_currency","restore_currency","restore_any_currency","replicate_currency","reorder_currency","delete_currency","delete_any_currency","force_delete_currency","force_delete_any_currency","view_delivery","view_any_delivery","create_delivery","update_delivery","restore_delivery","restore_any_delivery","replicate_delivery","reorder_delivery","delete_delivery","delete_any_delivery","force_delete_delivery","force_delete_any_delivery","view_exchange","view_any_exchange","create_exchange","update_exchange","restore_exchange","restore_any_exchange","replicate_exchange","reorder_exchange","delete_exchange","delete_any_exchange","force_delete_exchange","force_delete_any_exchange","view_order","view_any_order","create_order","update_order","restore_order","restore_any_order","replicate_order","reorder_order","delete_order","delete_any_order","force_delete_order","force_delete_any_order","view_payment","view_any_payment","create_payment","update_payment","restore_payment","restore_any_payment","replicate_payment","reorder_payment","delete_payment","delete_any_payment","force_delete_payment","force_delete_any_payment","view_product","view_any_product","create_product","update_product","restore_product","restore_any_product","replicate_product","reorder_product","delete_product","delete_any_product","force_delete_product","force_delete_any_product","view_region","view_any_region","create_region","update_region","restore_region","restore_any_region","replicate_region","reorder_region","delete_region","delete_any_region","force_delete_region","force_delete_any_region","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_seller","view_any_seller","create_seller","update_seller","restore_seller","restore_any_seller","replicate_seller","reorder_seller","delete_seller","delete_any_seller","force_delete_seller","force_delete_any_seller","view_township","view_any_township","create_township","update_township","restore_township","restore_any_township","replicate_township","reorder_township","delete_township","delete_any_township","force_delete_township","force_delete_any_township","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $rolesWithPermissionsForSeller = '[{"name":"panel_user","guard_name":"seller","permissions":[]},{"name":"seller_admin","guard_name":"seller","permissions":["view_attribute","view_any_attribute","create_attribute","update_attribute","restore_attribute","restore_any_attribute","replicate_attribute","reorder_attribute","delete_attribute","delete_any_attribute","force_delete_attribute","force_delete_any_attribute","view_brand","view_any_brand","create_brand","update_brand","restore_brand","restore_any_brand","replicate_brand","reorder_brand","delete_brand","delete_any_brand","force_delete_brand","force_delete_any_brand","view_category","view_any_category","create_category","update_category","restore_category","restore_any_category","replicate_category","reorder_category","delete_category","delete_any_category","force_delete_category","force_delete_any_category","view_currency","view_any_currency","create_currency","update_currency","restore_currency","restore_any_currency","replicate_currency","reorder_currency","delete_currency","delete_any_currency","force_delete_currency","force_delete_any_currency","view_delivery","view_any_delivery","create_delivery","update_delivery","restore_delivery","restore_any_delivery","replicate_delivery","reorder_delivery","delete_delivery","delete_any_delivery","force_delete_delivery","force_delete_any_delivery","view_exchange","view_any_exchange","create_exchange","update_exchange","restore_exchange","restore_any_exchange","replicate_exchange","reorder_exchange","delete_exchange","delete_any_exchange","force_delete_exchange","force_delete_any_exchange","view_order","view_any_order","create_order","update_order","restore_order","restore_any_order","replicate_order","reorder_order","delete_order","delete_any_order","force_delete_order","force_delete_any_order","view_payment","view_any_payment","create_payment","update_payment","restore_payment","restore_any_payment","replicate_payment","reorder_payment","delete_payment","delete_any_payment","force_delete_payment","force_delete_any_payment","view_product","view_any_product","create_product","update_product","restore_product","restore_any_product","replicate_product","reorder_product","delete_product","delete_any_product","force_delete_product","force_delete_any_product","view_seller","view_any_seller","create_seller","update_seller","restore_seller","restore_any_seller","replicate_seller","reorder_seller","delete_seller","delete_any_seller","force_delete_seller","force_delete_any_seller","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user"]}]';
        $directPermissionsForSeller = '[]';

        static::makeRolesWithPermissions($rolesWithPermissionsForSeller);
        static::makeDirectPermissions($directPermissionsForSeller);



        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
