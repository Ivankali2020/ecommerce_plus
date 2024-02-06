<?php

namespace App\Http\Middleware;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Exchange;
use App\Models\Payment;
use App\Models\Product;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssignGlobeScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        Brand::addGlobalScope(function ($q){
            //to add for parent_id
//            if(Filament::auth()->user()->parent_id){
//
//            }
            return $q->where('seller_id',Filament::auth()->id());
        });

        Attribute::addGlobalScope(function ($q){
            //to add for parent_id
//            if(Filament::auth()->user()->parent_id){
//
//            }
            return $q->where('seller_id',Filament::auth()->id());
        });

        Category::addGlobalScope(function ($q){
            //to add for parent_id
//            if(Filament::auth()->user()->parent_id){
//
//            }
            return $q->where('seller_id',Filament::auth()->id());
        });

        Product::addGlobalScope(function ($q){
            //to add for parent_id
//            if(Filament::auth()->user()->parent_id){
//
//            }
            return $q->where('seller_id',Filament::auth()->id());
        });

        Exchange::addGlobalScope(function ($q){
            //to add for parent_id
//            if(Filament::auth()->user()->parent_id){
//
//            }
            return $q->where('seller_id',Filament::auth()->id());
        });

        Payment::addGlobalScope(function ($q){
            //to add for parent_id
//            if(Filament::auth()->user()->parent_id){
//
//            }
            return $q->where('seller_id',Filament::auth()->id());
        });

        return $next($request);
    }
}
