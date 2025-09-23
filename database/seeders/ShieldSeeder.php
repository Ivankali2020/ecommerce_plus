<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"seller","permissions":["ViewAny:Admin","View:Admin","Create:Admin","Update:Admin","Delete:Admin","Restore:Admin","ForceDelete:Admin","ForceDeleteAny:Admin","RestoreAny:Admin","Replicate:Admin","Reorder:Admin","ViewAny:Attribute","View:Attribute","Create:Attribute","Update:Attribute","Delete:Attribute","Restore:Attribute","ForceDelete:Attribute","ForceDeleteAny:Attribute","RestoreAny:Attribute","Replicate:Attribute","Reorder:Attribute","ViewAny:Brand","View:Brand","Create:Brand","Update:Brand","Delete:Brand","Restore:Brand","ForceDelete:Brand","ForceDeleteAny:Brand","RestoreAny:Brand","Replicate:Brand","Reorder:Brand","ViewAny:Category","View:Category","Create:Category","Update:Category","Delete:Category","Restore:Category","ForceDelete:Category","ForceDeleteAny:Category","RestoreAny:Category","Replicate:Category","Reorder:Category","ViewAny:Currency","View:Currency","Create:Currency","Update:Currency","Delete:Currency","Restore:Currency","ForceDelete:Currency","ForceDeleteAny:Currency","RestoreAny:Currency","Replicate:Currency","Reorder:Currency","ViewAny:Delivery","View:Delivery","Create:Delivery","Update:Delivery","Delete:Delivery","Restore:Delivery","ForceDelete:Delivery","ForceDeleteAny:Delivery","RestoreAny:Delivery","Replicate:Delivery","Reorder:Delivery","ViewAny:Domain","View:Domain","Create:Domain","Update:Domain","Delete:Domain","Restore:Domain","ForceDelete:Domain","ForceDeleteAny:Domain","RestoreAny:Domain","Replicate:Domain","Reorder:Domain","ViewAny:Exchange","View:Exchange","Create:Exchange","Update:Exchange","Delete:Exchange","Restore:Exchange","ForceDelete:Exchange","ForceDeleteAny:Exchange","RestoreAny:Exchange","Replicate:Exchange","Reorder:Exchange","ViewAny:Order","View:Order","Create:Order","Update:Order","Delete:Order","Restore:Order","ForceDelete:Order","ForceDeleteAny:Order","RestoreAny:Order","Replicate:Order","Reorder:Order","ViewAny:Payment","View:Payment","Create:Payment","Update:Payment","Delete:Payment","Restore:Payment","ForceDelete:Payment","ForceDeleteAny:Payment","RestoreAny:Payment","Replicate:Payment","Reorder:Payment","ViewAny:Product","View:Product","Create:Product","Update:Product","Delete:Product","Restore:Product","ForceDelete:Product","ForceDeleteAny:Product","RestoreAny:Product","Replicate:Product","Reorder:Product","ViewAny:Region","View:Region","Create:Region","Update:Region","Delete:Region","Restore:Region","ForceDelete:Region","ForceDeleteAny:Region","RestoreAny:Region","Replicate:Region","Reorder:Region","ViewAny:SellerSubscription","View:SellerSubscription","Create:SellerSubscription","Update:SellerSubscription","Delete:SellerSubscription","Restore:SellerSubscription","ForceDelete:SellerSubscription","ForceDeleteAny:SellerSubscription","RestoreAny:SellerSubscription","Replicate:SellerSubscription","Reorder:SellerSubscription","ViewAny:Seller","View:Seller","Create:Seller","Update:Seller","Delete:Seller","Restore:Seller","ForceDelete:Seller","ForceDeleteAny:Seller","RestoreAny:Seller","Replicate:Seller","Reorder:Seller","ViewAny:SubscriptionPlan","View:SubscriptionPlan","Create:SubscriptionPlan","Update:SubscriptionPlan","Delete:SubscriptionPlan","Restore:SubscriptionPlan","ForceDelete:SubscriptionPlan","ForceDeleteAny:SubscriptionPlan","RestoreAny:SubscriptionPlan","Replicate:SubscriptionPlan","Reorder:SubscriptionPlan","ViewAny:Township","View:Township","Create:Township","Update:Township","Delete:Township","Restore:Township","ForceDelete:Township","ForceDeleteAny:Township","RestoreAny:Township","Replicate:Township","Reorder:Township","ViewAny:User","View:User","Create:User","Update:User","Delete:User","Restore:User","ForceDelete:User","ForceDeleteAny:User","RestoreAny:User","Replicate:User","Reorder:User","ViewAny:Expanse","View:Expanse","Create:Expanse","Update:Expanse","Delete:Expanse","Restore:Expanse","ForceDelete:Expanse","ForceDeleteAny:Expanse","RestoreAny:Expanse","Replicate:Expanse","Reorder:Expanse","ViewAny:GiftExchange","View:GiftExchange","Delete:GiftExchange","Restore:GiftExchange","RestoreAny:GiftExchange","Replicate:GiftExchange","Reorder:GiftExchange","ViewAny:Gift","View:Gift","Create:Gift","Update:Gift","Delete:Gift","Restore:Gift","ForceDelete:Gift","ForceDeleteAny:Gift","RestoreAny:Gift","Replicate:Gift","Reorder:Gift","ViewAny:OrderProduct","View:OrderProduct","Delete:OrderProduct","ViewAny:OrderReturn","View:OrderReturn","Create:OrderReturn","Update:OrderReturn","Delete:OrderReturn","Restore:OrderReturn","ForceDelete:OrderReturn","ForceDeleteAny:OrderReturn","RestoreAny:OrderReturn","Replicate:OrderReturn","Reorder:OrderReturn","ViewAny:PointTransaction","View:PointTransaction","Create:PointTransaction","Update:PointTransaction","Delete:PointTransaction","Restore:PointTransaction","ForceDelete:PointTransaction","ForceDeleteAny:PointTransaction","RestoreAny:PointTransaction","Replicate:PointTransaction","Reorder:PointTransaction","ViewAny:Printer","View:Printer","Create:Printer","Update:Printer","Delete:Printer","Restore:Printer","ForceDelete:Printer","ForceDeleteAny:Printer","RestoreAny:Printer","Replicate:Printer","Reorder:Printer","ViewAny:Purchase","View:Purchase","Create:Purchase","Update:Purchase","Delete:Purchase","Restore:Purchase","ForceDelete:Purchase","ForceDeleteAny:Purchase","RestoreAny:Purchase","Replicate:Purchase","Reorder:Purchase","ViewAny:ReferralHistory","View:ReferralHistory","Delete:ReferralHistory","Restore:ReferralHistory","ForceDelete:ReferralHistory","ForceDeleteAny:ReferralHistory","RestoreAny:ReferralHistory","Replicate:ReferralHistory","Reorder:ReferralHistory","ViewAny:SellerAddress","View:SellerAddress","Create:SellerAddress","Update:SellerAddress","Delete:SellerAddress","Restore:SellerAddress","ForceDelete:SellerAddress","ForceDeleteAny:SellerAddress","RestoreAny:SellerAddress","Replicate:SellerAddress","Reorder:SellerAddress","ViewAny:Setting","View:Setting","Create:Setting","Update:Setting","Delete:Setting","Restore:Setting","ForceDelete:Setting","ForceDeleteAny:Setting","RestoreAny:Setting","Replicate:Setting","Reorder:Setting","ViewAny:Supplier","View:Supplier","Create:Supplier","Update:Supplier","Delete:Supplier","Restore:Supplier","ForceDelete:Supplier","ForceDeleteAny:Supplier","RestoreAny:Supplier","Replicate:Supplier","Reorder:Supplier","View:Barcode","View:Filters","View:StatsOverview","View:BestSellerProduct","View:OrderByRegion","View:OrderChart"]},{"name":"panel_user","guard_name":"admin","permissions":[]}]';
        $directPermissions = '{"200":{"name":"Create:GiftExchange","guard_name":"seller"},"201":{"name":"Update:GiftExchange","guard_name":"seller"},"204":{"name":"ForceDelete:GiftExchange","guard_name":"seller"},"205":{"name":"ForceDeleteAny:GiftExchange","guard_name":"seller"},"222":{"name":"Create:OrderProduct","guard_name":"seller"},"223":{"name":"Update:OrderProduct","guard_name":"seller"},"225":{"name":"Restore:OrderProduct","guard_name":"seller"},"226":{"name":"ForceDelete:OrderProduct","guard_name":"seller"},"227":{"name":"ForceDeleteAny:OrderProduct","guard_name":"seller"},"228":{"name":"RestoreAny:OrderProduct","guard_name":"seller"},"229":{"name":"Replicate:OrderProduct","guard_name":"seller"},"230":{"name":"Reorder:OrderProduct","guard_name":"seller"},"277":{"name":"Create:ReferralHistory","guard_name":"seller"},"278":{"name":"Update:ReferralHistory","guard_name":"seller"},"319":{"name":"ViewAny:Role","guard_name":"seller"},"320":{"name":"View:Role","guard_name":"seller"},"321":{"name":"Create:Role","guard_name":"seller"},"322":{"name":"Update:Role","guard_name":"seller"},"323":{"name":"Delete:Role","guard_name":"seller"},"324":{"name":"Restore:Role","guard_name":"seller"},"325":{"name":"ForceDelete:Role","guard_name":"seller"},"326":{"name":"ForceDeleteAny:Role","guard_name":"seller"},"327":{"name":"RestoreAny:Role","guard_name":"seller"},"328":{"name":"Replicate:Role","guard_name":"seller"},"329":{"name":"Reorder:Role","guard_name":"seller"}}';

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
