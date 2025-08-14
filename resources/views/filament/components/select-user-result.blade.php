<div class="flex rounded-md relative">
    <div class="flex justify-between items-center ">
        <div class="flex flex-col justify-center pl-3 ">
            <p class="text-sm font-bold pb-2 ">{{ $name }}</p>
            <div class="flex gap-4 ">
                <p class="text-xs  bg-amber-50 pr-2  leading-5">{{ $sku ?? '' }}</p>
                <p class="text-xs pr-2  leading-5">{{ $brand }} </p>
                <p class="text-xs leading-5">{{ $category }} </p>
            </div>
        </div>
    </div>
</div>
