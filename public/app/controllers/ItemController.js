app.controller('ItemController', function ($scope, $http, $timeout) {
    $scope.items = [];
    $scope.total = 0;
    $scope.currentPage = 1;
    $scope.lastPage = 1;
    $scope.activeCount = 0;
    $scope.inactiveCount = 0;
    $scope.categories = [];
    $scope.loading = false;
    $scope.toasts = [];
    $scope.form = {};
    $scope.formTitle = 'Add Item';
    $scope.selectedItems = {};
    $scope.selectAll = false;
    $scope.suggestions = [];
    $scope.showSuggestions = false;
    $scope.detailItem = null;
    $scope.deleteItem = null;
    $scope.deleteItemTitle = '';
    $scope.bulkStatusValue = 1;
    $scope.sortBy = '';
    $scope.sortDirIcon = '↕';
    $scope.sortLabel = 'Default';

    var toastTimeout = function (index) {
        $timeout(function () {
            if ($scope.toasts[index]) { $scope.toasts[index].show = false; }
        }, 3000);
    };

    function showToast(message, type) {
        var iconMap = { success: 'fa-circle-check', error: 'fa-circle-xmark', info: 'fa-circle-info' };
        var toast = { message: message, type: type, icon: iconMap[type], show: true };
        $scope.toasts.push(toast);
        var idx = $scope.toasts.length - 1;
        toastTimeout(idx);
    }

    function loadCategories() {
        $http.get('/categories').then(function (response) {
            $scope.categories = response.data.data;
        });
    }

    function updateSortLabel() {
        var labels = { newest: 'Newest', oldest: 'Oldest', price_asc: 'Price: Low to High', price_desc: 'Price: High to Low' };
        $scope.sortLabel = labels[$scope.sortBy] || 'Default';
        $scope.sortDirIcon = ($scope.sortBy == 'price_asc') ? '↑' : ($scope.sortBy == 'price_desc') ? '↓' : '↕';
    }

    $scope.search = function (page) {
        page = page || 1;
        $scope.loading = true;
        $scope.selectAll = false;
        $scope.selectedItems = {};
        var params = {
            page: page,
            search: $scope.searchText,
            category_id: $scope.categoryId,
            min_price: $scope.minPrice,
            max_price: $scope.maxPrice,
            status: $scope.status,
            date_from: $scope.dateFrom,
            date_to: $scope.dateTo,
            sort_by: $scope.sortBy || 'newest'
        };
        $http({ method: 'GET', url: '/items', params: params }).then(function (response) {
            $scope.items = response.data.data;
            $scope.total = response.data.total;
            $scope.lastPage = response.data.last_page;
            $scope.currentPage = page;
            $scope.activeCount = 0;
            $scope.inactiveCount = 0;
            angular.forEach($scope.items, function (item) {
                if (item.status == 1) { $scope.activeCount++; }
                else { $scope.inactiveCount++; }
            });
            updateSortLabel();
        }).catch(function () {
            showToast('Error loading data', 'error');
        }).finally(function () {
            $scope.loading = false;
        });
    };

    $scope.nextPage = function () { if ($scope.currentPage < $scope.lastPage) { $scope.search($scope.currentPage + 1); } };
    $scope.prevPage = function () { if ($scope.currentPage > 1) { $scope.search($scope.currentPage - 1); } };
    $scope.goToPage = function (p) { if (p >= 1 && p <= $scope.lastPage) { $scope.search(p); } };

    $scope.pageNumbers = function () {
        var pages = [];
        var current = $scope.currentPage;
        var last = $scope.lastPage;
        var delta = 2;
        var left = Math.max(2, current - delta);
        var right = Math.min(last - 1, current + delta);
        pages.push(1);
        if (left > 2) { pages.push('...'); }
        for (var i = left; i <= right; i++) { pages.push(i); }
        if (right < last - 1) { pages.push('...'); }
        if (last > 1) { pages.push(last); }
        return pages;
    };

    $scope.setSort = function (field) {
        if (field == 'price') {
            if ($scope.sortBy == 'price_asc') { $scope.sortBy = 'price_desc'; }
            else { $scope.sortBy = 'price_asc'; }
        } else if (field == 'id') {
            $scope.sortBy = ($scope.sortBy == 'newest') ? 'oldest' : 'newest';
        }
        $scope.search(1);
        updateSortLabel();
    };

    $scope.exportCsv = function () { window.location.href = '/items/export/csv'; };

    $scope.isSelected = function (id) { return $scope.selectedItems[id] === true; };
    $scope.selectedCount = function () {
        var count = 0;
        angular.forEach($scope.selectedItems, function (v) { if (v === true) { count++; } });
        return count;
    };

    $scope.toggleSelect = function (id) {
        if ($scope.selectedItems[id]) { delete $scope.selectedItems[id]; }
        else { $scope.selectedItems[id] = true; }
        updateSelectAll();
    };
    $scope.toggleAll = function () {
        angular.forEach($scope.items, function (item) { $scope.selectedItems[item.id] = $scope.selectAll; });
    };
    function updateSelectAll() {
        var selected = 0;
        angular.forEach($scope.selectedItems, function (v) { if (v === true) { selected++; } });
        $scope.selectAll = (selected > 0 && selected === $scope.items.length);
    }

    $scope.openAddModal = function () {
        $scope.form = { title: '', description: '', price: '', status: 1, category_id: '', image: '', imagePreview: '', id: null };
        $scope.formTitle = 'Add New Item';
        $('#itemModal').modal('show');
    };

    $scope.openEditModal = function (item) {
        $scope.form = {
            id: item.id,
            title: item.title,
            description: item.description,
            price: item.price || '',
            status: item.status ? 1 : 0,
            category_id: item.category_id || '',
            image: item.image || '',
            imagePreview: item.image ? '/storage/' + item.image : ''
        };
        $scope.formTitle = 'Edit Item';
        $('#itemModal').modal('show');
    };

    $scope.saveItem = function () {
        $scope.loading = true;
        var url = $scope.form.id ? '/items/' + $scope.form.id : '/items';
        var method = $scope.form.id ? 'PUT' : 'POST';
        var data = {
            title: $scope.form.title,
            description: $scope.form.description,
            price: $scope.form.price,
            status: $scope.form.status ? 1 : 0,
            category_id: $scope.form.category_id || null,
            image: $scope.form.image || null
        };
        $http({ method: method, url: url, data: data }).then(function (response) {
            $('#itemModal').modal('hide');
            showToast($scope.form.id ? 'Item updated successfully' : 'Item added successfully', 'success');
            $scope.search($scope.currentPage);
        }).catch(function (error) {
            if (error.status === 422 && error.data.errors) {
                var messages = [];
                angular.forEach(error.data.errors, function (msgs, field) {
                    angular.forEach(msgs, function (msg) { messages.push(msg); });
                });
                showToast(messages.join(', '), 'error');
            } else {
                showToast('Error saving item', 'error');
            }
        }).finally(function () {
            $scope.loading = false;
        });
    };

    $scope.confirmDelete = function (item) {
        $scope.deleteItem = item;
        $scope.deleteItemTitle = item.title;
        $('#deleteModal').modal('show');
    };

    $scope.deleteItemAction = function () {
        $scope.loading = true;
        $http.delete('/items/' + $scope.deleteItem.id).then(function () {
            $('#deleteModal').modal('hide');
            showToast('Item deleted successfully', 'success');
            $scope.deleteItem = null;
            $scope.search($scope.currentPage);
        }).catch(function (error) {
            showToast('Error deleting item', 'error');
        }).finally(function () {
            $scope.loading = false;
        });
    };

    $scope.uploadImage = function (input) {
        var file = input.files[0];
        if (!file) return;
        var fd = new FormData();
        fd.append('image', file);
        $http.post('/items/upload-image', fd, {
            transformRequest: angular.identity,
            headers: { 'Content-Type': undefined }
        }).then(function (response) {
            $scope.form.image = response.data.path;
            $scope.form.imagePreview = '/storage/' + response.data.path;
        }).catch(function () {
            showToast('Image upload failed', 'error');
        });
    };

    $scope.viewDetails = function (item) {
        $http.get('/items/' + item.id).then(function (response) {
            $scope.detailItem = response.data.data;
            $('#detailModal').modal('show');
        });
    };

    $scope.openBulkDeleteModal = function () {
        var selectedIds = Object.keys($scope.selectedItems).filter(function (k) { return $scope.selectedItems[k] === true; });
        if (selectedIds.length === 0) { showToast('No items selected', 'info'); return; }
        $('#bulkDeleteModal').modal('show');
    };
    $scope.bulkDelete = function () {
        $scope.loading = true;
        var ids = Object.keys($scope.selectedItems).filter(function (k) { return $scope.selectedItems[k] === true; });
        $http.post('/items/bulk-delete', { ids: ids }).then(function () {
            $('#bulkDeleteModal').modal('hide');
            showToast('Bulk delete successful', 'success');
            $scope.search(1);
        }).catch(function (error) {
            showToast('Error deleting items', 'error');
        }).finally(function () {
            $scope.loading = false;
        });
    };

    $scope.openBulkStatusModal = function () {
        var selectedIds = Object.keys($scope.selectedItems).filter(function (k) { return $scope.selectedItems[k] === true; });
        if (selectedIds.length === 0) { showToast('No items selected', 'info'); return; }
        $('#bulkStatusModal').modal('show');
    };
    $scope.bulkChangeStatus = function () {
        $scope.loading = true;
        var ids = Object.keys($scope.selectedItems).filter(function (k) { return $scope.selectedItems[k] === true; });
        $http.post('/items/bulk-status', { ids: ids, status: $scope.bulkStatusValue }).then(function () {
            $('#bulkStatusModal').modal('hide');
            showToast('Bulk status updated', 'success');
            $scope.search($scope.currentPage);
        }).catch(function (error) {
            showToast('Error updating status', 'error');
        }).finally(function () {
            $scope.loading = false;
        });
    };

    $scope.onSearchChange = function () {
        $timeout.cancel($scope.searchTimer);
        $scope.searchTimer = $timeout(function () {
            if ($scope.searchText && $scope.searchText.length >= 2) {
                $http.get('/items/suggestions', { params: { q: $scope.searchText } }).then(function (response) {
                    $scope.suggestions = response.data.data;
                    $scope.showSuggestions = true;
                });
            } else {
                $scope.suggestions = [];
                $scope.showSuggestions = false;
            }
        }, 300);
    };

    $scope.selectSuggestion = function (s) {
        $scope.searchText = s.title;
        $scope.showSuggestions = false;
        $scope.suggestions = [];
        $scope.search(1);
    };

    $scope.$watch('categoryId', function () { $scope.search(1); });
    $scope.$watch('status', function () { $scope.search(1); });
    $scope.$watch('minPrice', function () { $scope.search(1); });
    $scope.$watch('maxPrice', function () { $scope.search(1); });
    $scope.$watch('dateFrom', function () { $scope.search(1); });
    $scope.$watch('dateTo', function () { $scope.search(1); });
    $scope.$watch('sortBy', function () { if ($scope.sortBy) { $scope.search(1); } });

    loadCategories();
    $scope.search();
});
