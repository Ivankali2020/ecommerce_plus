<div class="fi-section fi-color-danger">
    <div class="fi-section-content" style="padding: 1.5rem;">
        <div style="display: flex; align-items: center; margin-bottom: 1rem;">
            <div style="flex-shrink: 0;">
                <x-heroicon-o-exclamation-triangle class="fi-icon fi-color-danger" style="width: 2rem; height: 2rem;" />
            </div>
            <div style="margin-left: 0.75rem;">
                <h3 class="fi-section-header-heading fi-color-danger">Error Loading Product Preview</h3>
            </div>
        </div>
        
        <div class="fi-card fi-color-gray" style="padding: 1rem; margin-bottom: 1rem;">
            <h4 class="fi-section-header-heading" style="margin-bottom: 0.5rem;">Error Details:</h4>
            <p class="fi-body" style="font-family: 'Courier New', monospace; color: var(--danger-600);">{{ $error }}</p>
        </div>

        <div class="fi-card fi-color-gray" style="padding: 1rem;">
            <h4 class="fi-section-header-heading" style="margin-bottom: 0.5rem;">Applied Filters:</h4>
            @if(!empty($filters))
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($filters as $filterKey => $filterValue)
                        @if(!empty($filterValue))
                            <span class="fi-badge fi-color-danger fi-size-sm">
                                {{ ucfirst(str_replace('_', ' ', $filterKey)) }}: 
                                @if(is_array($filterValue))
                                    {{ implode(', ', array_filter($filterValue, fn($v) => !empty($v))) }}
                                @else
                                    {{ $filterValue }}
                                @endif
                            </span>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="fi-descriptive-label fi-color-danger">No filters applied</p>
            @endif
        </div>

        <div style="margin-top: 1rem;">
            <p class="fi-descriptive-label fi-color-danger">
                Please try refreshing the page or contact the administrator if this error persists.
            </p>
        </div>
    </div>
</div>
