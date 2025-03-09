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

        $rolesWithPermissions = '[{"name":"panel_user","guard_name":"seller","permissions":[]},{"name":"super_admin","guard_name":"seller","permissions":["view_attribute","view_any_attribute","create_attribute","update_attribute","restore_attribute","restore_any_attribute","replicate_attribute","reorder_attribute","delete_attribute","delete_any_attribute","force_delete_attribute","force_delete_any_attribute","view_brand","view_any_brand","create_brand","update_brand","restore_brand","restore_any_brand","replicate_brand","reorder_brand","delete_brand","delete_any_brand","force_delete_brand","force_delete_any_brand","view_category","view_any_category","create_category","update_category","restore_category","restore_any_category","replicate_category","reorder_category","delete_category","delete_any_category","force_delete_category","force_delete_any_category","view_delivery","view_any_delivery","create_delivery","update_delivery","restore_delivery","restore_any_delivery","replicate_delivery","reorder_delivery","delete_delivery","delete_any_delivery","force_delete_delivery","force_delete_any_delivery","view_exchange","view_any_exchange","create_exchange","update_exchange","restore_exchange","restore_any_exchange","replicate_exchange","reorder_exchange","delete_exchange","delete_any_exchange","force_delete_exchange","force_delete_any_exchange","view_expanse","view_any_expanse","create_expanse","update_expanse","restore_expanse","restore_any_expanse","replicate_expanse","reorder_expanse","delete_expanse","delete_any_expanse","force_delete_expanse","force_delete_any_expanse","view_order","view_any_order","create_order","update_order","restore_order","restore_any_order","replicate_order","reorder_order","delete_order","delete_any_order","force_delete_order","force_delete_any_order","view_order::product","view_any_order::product","create_order::product","update_order::product","restore_order::product","restore_any_order::product","replicate_order::product","reorder_order::product","delete_order::product","delete_any_order::product","force_delete_order::product","force_delete_any_order::product","view_payment","view_any_payment","create_payment","update_payment","restore_payment","restore_any_payment","replicate_payment","reorder_payment","delete_payment","delete_any_payment","force_delete_payment","force_delete_any_payment","view_product","view_any_product","create_product","update_product","restore_product","restore_any_product","replicate_product","reorder_product","delete_product","delete_any_product","force_delete_product","force_delete_any_product","view_purchase","view_any_purchase","create_purchase","update_purchase","restore_purchase","restore_any_purchase","replicate_purchase","reorder_purchase","delete_purchase","delete_any_purchase","force_delete_purchase","force_delete_any_purchase","view_seller::address","view_any_seller::address","create_seller::address","update_seller::address","restore_seller::address","restore_any_seller::address","replicate_seller::address","reorder_seller::address","delete_seller::address","delete_any_seller::address","force_delete_seller::address","force_delete_any_seller::address","view_staff","view_any_staff","create_staff","update_staff","restore_staff","restore_any_staff","replicate_staff","reorder_staff","delete_staff","delete_any_staff","force_delete_staff","force_delete_any_staff","view_supplier","view_any_supplier","create_supplier","update_supplier","restore_supplier","restore_any_supplier","replicate_supplier","reorder_supplier","delete_supplier","delete_any_supplier","force_delete_supplier","force_delete_any_supplier","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","page_Barcode","widget_StatsOverview","widget_OrderChart","widget_BestSellerProduct","widget_OrderByRegion"]}]';
        $directPermissions = '{"0":{"name":"view_admin","guard_name":"seller"},"1":{"name":"view_any_admin","guard_name":"seller"},"2":{"name":"create_admin","guard_name":"seller"},"3":{"name":"update_admin","guard_name":"seller"},"4":{"name":"restore_admin","guard_name":"seller"},"5":{"name":"restore_any_admin","guard_name":"seller"},"6":{"name":"replicate_admin","guard_name":"seller"},"7":{"name":"reorder_admin","guard_name":"seller"},"8":{"name":"delete_admin","guard_name":"seller"},"9":{"name":"delete_any_admin","guard_name":"seller"},"10":{"name":"force_delete_admin","guard_name":"seller"},"11":{"name":"force_delete_any_admin","guard_name":"seller"},"48":{"name":"view_currency","guard_name":"seller"},"49":{"name":"view_any_currency","guard_name":"seller"},"50":{"name":"create_currency","guard_name":"seller"},"51":{"name":"update_currency","guard_name":"seller"},"52":{"name":"restore_currency","guard_name":"seller"},"53":{"name":"restore_any_currency","guard_name":"seller"},"54":{"name":"replicate_currency","guard_name":"seller"},"55":{"name":"reorder_currency","guard_name":"seller"},"56":{"name":"delete_currency","guard_name":"seller"},"57":{"name":"delete_any_currency","guard_name":"seller"},"58":{"name":"force_delete_currency","guard_name":"seller"},"59":{"name":"force_delete_any_currency","guard_name":"seller"},"156":{"name":"view_region","guard_name":"seller"},"157":{"name":"view_any_region","guard_name":"seller"},"158":{"name":"create_region","guard_name":"seller"},"159":{"name":"update_region","guard_name":"seller"},"160":{"name":"restore_region","guard_name":"seller"},"161":{"name":"restore_any_region","guard_name":"seller"},"162":{"name":"replicate_region","guard_name":"seller"},"163":{"name":"reorder_region","guard_name":"seller"},"164":{"name":"delete_region","guard_name":"seller"},"165":{"name":"delete_any_region","guard_name":"seller"},"166":{"name":"force_delete_region","guard_name":"seller"},"167":{"name":"force_delete_any_region","guard_name":"seller"},"168":{"name":"view_role","guard_name":"seller"},"169":{"name":"view_any_role","guard_name":"seller"},"170":{"name":"create_role","guard_name":"seller"},"171":{"name":"update_role","guard_name":"seller"},"172":{"name":"delete_role","guard_name":"seller"},"173":{"name":"delete_any_role","guard_name":"seller"},"174":{"name":"view_seller","guard_name":"seller"},"175":{"name":"view_any_seller","guard_name":"seller"},"176":{"name":"create_seller","guard_name":"seller"},"177":{"name":"update_seller","guard_name":"seller"},"178":{"name":"restore_seller","guard_name":"seller"},"179":{"name":"restore_any_seller","guard_name":"seller"},"180":{"name":"replicate_seller","guard_name":"seller"},"181":{"name":"reorder_seller","guard_name":"seller"},"182":{"name":"delete_seller","guard_name":"seller"},"183":{"name":"delete_any_seller","guard_name":"seller"},"184":{"name":"force_delete_seller","guard_name":"seller"},"185":{"name":"force_delete_any_seller","guard_name":"seller"},"222":{"name":"view_township","guard_name":"seller"},"223":{"name":"view_any_township","guard_name":"seller"},"224":{"name":"create_township","guard_name":"seller"},"225":{"name":"update_township","guard_name":"seller"},"226":{"name":"restore_township","guard_name":"seller"},"227":{"name":"restore_any_township","guard_name":"seller"},"228":{"name":"replicate_township","guard_name":"seller"},"229":{"name":"reorder_township","guard_name":"seller"},"230":{"name":"delete_township","guard_name":"seller"},"231":{"name":"delete_any_township","guard_name":"seller"},"232":{"name":"force_delete_township","guard_name":"seller"},"233":{"name":"force_delete_any_township","guard_name":"seller"},"242":{"name":"delete_user","guard_name":"seller"},"243":{"name":"delete_any_user","guard_name":"seller"},"244":{"name":"force_delete_user","guard_name":"seller"},"245":{"name":"force_delete_any_user","guard_name":"seller"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

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
