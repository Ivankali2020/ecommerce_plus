// Refactored and commented for readability
(function () {
    "use strict";
    // Prevent multiple initializations
    window.modalSortingInitialized = false;
    let currentSort = { column: null, direction: null };
    let initializationInProgress = false;

    /**
     * Initializes table sorting for modal product table.
     * Attaches click listeners to sortable headers.
     * Returns true if initialized, false if elements not found.
     */
    function initializeTableSorting() {
        if (window.modalSortingInitialized) {
            console.log("Table sorting already initialized");
            return true;
        }
        const sortableHeaders = document.querySelectorAll(".sortable-header");
        const tbody = document.getElementById("products-tbody");
        if (!sortableHeaders.length || !tbody) {
            console.log("Sortable elements not found, will retry...");
            return false;
        }
        console.log(
            "Initializing table sorting with",
            sortableHeaders.length,
            "headers"
        );
        // Attach click listeners to each sortable header
        sortableHeaders.forEach((header) => {
            header.removeEventListener("click", handleSortClick);
            header.addEventListener("click", handleSortClick);
        });

        /**
         * Handles click event on sortable header.
         * Determines sort direction and triggers sorting.
         */
        function handleSortClick() {
            const sortType = this.dataset.sort;
            const isCurrentColumn = currentSort.column === sortType;
            console.log("Sorting by:", sortType);
            // Remove sort classes from all headers
            sortableHeaders.forEach((h) => {
                h.classList.remove("sort-asc", "sort-desc");
            });
            // Toggle direction if same column, otherwise default to asc
            let direction = "asc";
            if (isCurrentColumn && currentSort.direction === "asc") {
                direction = "desc";
            }
            currentSort = { column: sortType, direction };
            this.classList.add(`sort-${direction}`);
            sortTable(sortType, direction);
        }

        /**
         * Sorts the table rows based on the selected column and direction.
         * @param {string} sortType - The column to sort by (buy_price, sale_price, profit, quantity)
         * @param {string} direction - 'asc' or 'desc'
         */
        function sortTable(sortType, direction) {
            const rows = Array.from(tbody.querySelectorAll("tr"));
            console.log("Found rows for sorting:", rows.length);
            if (rows.length === 0) {
                console.log("No rows found to sort");
                return;
            }
            // Sorting logic for each supported column
            rows.sort((a, b) => {
                let aVal, bVal;
                switch (sortType) {
                    case "buy_price":
                        aVal =
                            parseFloat(a.getAttribute("data-buy-price")) || 0;
                        bVal =
                            parseFloat(b.getAttribute("data-buy-price")) || 0;
                        break;
                    case "sale_price":
                        aVal =
                            parseFloat(a.getAttribute("data-sale-price")) || 0;
                        bVal =
                            parseFloat(b.getAttribute("data-sale-price")) || 0;
                        break;
                    case "profit":
                        aVal = parseFloat(a.getAttribute("data-profit")) || 0;
                        bVal = parseFloat(b.getAttribute("data-profit")) || 0;
                        break;
                    case "quantity":
                        aVal = parseFloat(a.getAttribute("data-quantity")) || 0;
                        bVal = parseFloat(b.getAttribute("data-quantity")) || 0;
                        break;
                    default:
                        return 0;
                }
                return direction === "asc" ? aVal - bVal : bVal - aVal;
            });
            // Animate opacity during sorting
            tbody.style.opacity = "0.7";
            rows.forEach((row) => tbody.appendChild(row));
            setTimeout(() => {
                tbody.style.opacity = "1";
            }, 150);
            console.log(
                "Sorting completed for",
                sortType,
                "in",
                direction,
                "order"
            );
        }
        window.modalSortingInitialized = true;
        console.log("Table sorting initialized successfully");
        return true;
    }

    // Expose initialization and test functions globally
    window.initializeTableSorting = initializeTableSorting;
    window.resetSortingInitialization = resetInitialization;

    /**
     * Test utility for modal sorting. Shows alert with current state and triggers first header sort.
     */
    window.testModalSorting = function () {
        const headers = document.querySelectorAll(".sortable-header");
        const tbody = document.getElementById("products-tbody");
        const rows = tbody ? tbody.querySelectorAll("tr") : [];
        const message = `Modal Sorting Test:\n- Headers found: ${
            headers.length
        }\n- Table body found: ${tbody ? "Yes" : "No"}\n- Rows found: ${
            rows.length
        }\n- Initialized: ${window.modalSortingInitialized}`;
        console.log(message);
        alert(message);
        if (headers.length > 0) {
            console.log("Triggering first header click...");
            headers[0].click();
        }
    };

    /**
     * Attempts to initialize table sorting, retrying if elements are not yet available.
     * Prevents multiple simultaneous initialization attempts.
     */
    function attemptInitialization() {
        // Check if elements exist first before checking initialization status
        const sortableHeaders = document.querySelectorAll(".sortable-header");
        const tbody = document.getElementById("products-tbody");

        // If elements don't exist, don't bother checking initialization status
        if (!sortableHeaders.length || !tbody) {
            console.log("Sortable elements not found, skipping initialization");
            return;
        }

        // If elements exist but already initialized, check if they still have listeners
        if (window.modalSortingInitialized) {
            console.log("Already initialized, checking if still functional...");
            return;
        }

        // Prevent multiple initialization attempts running simultaneously
        if (initializationInProgress) {
            console.log("Initialization already in progress");
            return;
        }

        initializationInProgress = true;
        let attempts = 0;
        const maxAttempts = 20;

        function tryInit() {
            attempts++;
            console.log(`Initialization attempt ${attempts}/${maxAttempts}`);

            if (initializeTableSorting()) {
                console.log(
                    "Table sorting initialized successfully on attempt",
                    attempts
                );
                initializationInProgress = false;
                return;
            }

            if (attempts < maxAttempts) {
                setTimeout(tryInit, 200);
            } else {
                console.warn(
                    "Failed to initialize table sorting after",
                    maxAttempts,
                    "attempts"
                );
                initializationInProgress = false;
            }
        }
        tryInit();
    }

    /**
     * Reset initialization state when modal is closed
     */
    function resetInitialization() {
        window.modalSortingInitialized = false;
        initializationInProgress = false;
        currentSort = { column: null, direction: null };
        console.log("Sorting initialization reset");
    }

    // Attempt initialization on load and on various events (Livewire, Alpine, modal open)
    // Use debounced calls to prevent multiple simultaneous attempts
    // attemptInitialization();
    // document.addEventListener("DOMContentLoaded", attemptInitialization);

    if (typeof Livewire !== "undefined") {
        Livewire.hook("message.processed", () => {
            console.log("Livewire message processed, attempting sort init...");
            setTimeout(attemptInitialization, 100);
        });
    }

    if (typeof Alpine !== "undefined") {
        document.addEventListener("alpine:init", attemptInitialization);
    }

    document.addEventListener("open-modal", () => {
        console.log("Modal opened, attempting sort init...");
        setTimeout(attemptInitialization, 300);
    });

    // Reset when modal is closed
    document.addEventListener("close-modal", () => {
        console.log("Modal closed, resetting initialization...");
        resetInitialization();
    });

    // Also listen for Filament modal events
    document.addEventListener("modal-opened", () => {
        console.log("Filament modal opened, attempting sort init...");
        setTimeout(attemptInitialization, 300);
    });

    document.addEventListener("modal-closed", () => {
        console.log("Filament modal closed, resetting initialization...");
        resetInitialization();
    });
})();
