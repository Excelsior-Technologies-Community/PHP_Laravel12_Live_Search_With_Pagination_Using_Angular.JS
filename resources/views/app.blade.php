<!DOCTYPE html>
<html lang="en" ng-app="main-App">
<head>
    <meta charset="UTF-8">
    <title>Modern Item Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { transition: 0.3s; }
        body { background: linear-gradient(135deg, #141e30, #243b55); min-height: 100vh; font-family: Arial, sans-serif; padding: 30px 0; color: white; }
        .dashboard-card { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border-radius: 20px; padding: 25px; box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
        .dashboard-title { font-size: 30px; font-weight: bold; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .top-stats { display: flex; gap: 20px; margin-bottom: 25px; }
        .stat-box { flex: 1; padding: 20px; border-radius: 15px; text-align: center; color: white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .stat-box h2 { margin: 0; font-size: 32px; font-weight: bold; }
        .stat-box p { margin-top: 8px; font-size: 14px; }
        .bg-blue { background: linear-gradient(45deg, #2193b0, #6dd5ed); }
        .bg-green { background: linear-gradient(45deg, #11998e, #38ef7d); }
        .bg-red { background: linear-gradient(45deg, #cb2d3e, #ef473a); }
        .filter-box { background: rgba(255,255,255,0.07); padding: 20px; border-radius: 15px; margin-bottom: 15px; }
        .form-control { border-radius: 12px; border: none; height: 45px; background: rgba(255,255,255,0.15); color: white; }
        .form-control::placeholder { color: #ddd; }
        select.form-control option { color: black; }
        .form-control:focus { background: rgba(255,255,255,0.25); color: white; box-shadow: none; }
        .btn-custom { border: none; border-radius: 12px; height: 45px; font-weight: bold; }
        .btn-search { background: linear-gradient(45deg, #36d1dc, #5b86e5); color: white; }
        .btn-export { background: linear-gradient(45deg, #11998e, #38ef7d); color: white; }
        .btn-add { background: linear-gradient(45deg, #8e2de2, #4a00e0); color: white; }
        .btn-danger-custom { background: linear-gradient(45deg, #cb2d3e, #ef473a); color: white; }
        .btn-success-custom { background: linear-gradient(45deg, #11998e, #38ef7d); color: white; }
        .table-box { overflow-x: auto; }
        .table { background: rgba(255,255,255,0.05); border-radius: 15px; overflow: hidden; }
        .table th { background: rgba(255,255,255,0.1); color: white; border: none !important; padding: 15px !important; cursor: pointer; user-select: none; }
        .table th:hover { background: rgba(255,255,255,0.2); }
        .table td { color: #f1f1f1; border-color: rgba(255,255,255,0.05) !important; padding: 15px !important; vertical-align: middle; }
        .table tbody tr:hover { background: rgba(255,255,255,0.08); }
        .badge-active { background: #16a34a; padding: 7px 15px; border-radius: 30px; font-size: 12px; }
        .badge-inactive { background: #dc2626; padding: 7px 15px; border-radius: 30px; font-size: 12px; }
        .pagination-section { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 10px; }
        .page-btn { border: none; padding: 10px 20px; border-radius: 12px; background: rgba(255,255,255,0.1); color: white; }
        .page-btn:hover { background: rgba(255,255,255,0.2); }
        .page-btn:disabled { opacity: 0.3; cursor: not-allowed; }
        .page-numbers { display: flex; gap: 5px; }
        .page-num { width: 35px; height: 35px; border-radius: 10px; border: none; background: rgba(255,255,255,0.1); color: white; }
        .page-num.active { background: linear-gradient(45deg, #36d1dc, #5b86e5); }
        .page-num:hover:not(.active) { background: rgba(255,255,255,0.2); }
        .sort-icon { margin-left: 5px; font-size: 12px; }
        .action-btns { display: flex; gap: 5px; }
        .action-btn { width: 32px; height: 32px; border-radius: 8px; border: none; color: white; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
        .action-btn.edit { background: #36d1dc; }
        .action-btn.view { background: #11998e; }
        .action-btn.delete { background: #dc2626; }
        .checkbox-cell { width: 40px; text-align: center; }
        .custom-checkbox { width: 18px; height: 18px; cursor: pointer; accent-color: #5b86e5; }
        .image-preview { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; cursor: pointer; border: 2px solid rgba(255,255,255,0.2); }
        .no-image { width: 60px; height: 60px; border-radius: 10px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 24px; }
        .loading-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 9999; }
        .spinner { width: 60px; height: 60px; border: 5px solid rgba(255,255,255,0.2); border-top-color: #5b86e5; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9998; display: flex; flex-direction: column; gap: 10px; }
        .toast { padding: 15px 20px; border-radius: 12px; color: white; font-weight: bold; box-shadow: 0 5px 20px rgba(0,0,0,0.3); animation: slideIn 0.3s ease; max-width: 350px; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .toast.success { background: linear-gradient(45deg, #11998e, #38ef7d); }
        .toast.error { background: linear-gradient(45deg, #cb2d3e, #ef473a); }
        .toast.info { background: linear-gradient(45deg, #36d1dc, #5b86e5); }
        .modal-content { background: linear-gradient(135deg, #1a2a3a, #243b55); border-radius: 20px; color: white; }
        .modal-header { border-bottom: 1px solid rgba(255,255,255,0.1); }
        .modal-footer { border-top: 1px solid rgba(255,255,255,0.1); }
        .close-btn { color: white; opacity: 0.7; }
        .close-btn:hover { opacity: 1; }
        .detail-image { max-width: 100%; max-height: 300px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); }
        .detail-row { display: flex; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .detail-label { width: 150px; font-weight: bold; color: #8eb8e5; }
        .detail-value { flex: 1; }
    </style>
</head>
<body ng-controller="ItemController">
    <div class="container">
        <div class="dashboard-card">
            <div class="dashboard-title"><i class="fa-solid fa-box"></i> Modern Item Dashboard</div>
            <div class="top-stats">
                <div class="stat-box bg-blue"><h2>@{{ total }}</h2><p>Total Items</p></div>
                <div class="stat-box bg-green"><h2>@{{ activeCount }}</h2><p>Active Items</p></div>
                <div class="stat-box bg-red"><h2>@{{ inactiveCount }}</h2><p>Inactive Items</p></div>
            </div>
            <div class="filter-box">
                <div class="row">
                    <div class="col-md-3 search-wrapper">
                        <input type="text" class="form-control" placeholder="Search items..." ng-model="searchText" ng-change="onSearchChange()" autocomplete="off">
                        <div class="suggestions-box" ng-if="showSuggestions && suggestions.length > 0">
                            <div class="suggestion-item" ng-repeat="s in suggestions" ng-click="selectSuggestion(s)">@{{ s.title }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" ng-model="categoryId">
                            <option value="">All Categories</option>
                            <option ng-repeat="cat in categories" value="@{{ cat.id }}">@{{ cat.name }}</option>
                        </select>
                    </div>
                    <div class="col-md-2"><input type="number" class="form-control" placeholder="Min Price" ng-model="minPrice"></div>
                    <div class="col-md-2"><input type="number" class="form-control" placeholder="Max Price" ng-model="maxPrice"></div>
                    <div class="col-md-2">
                        <select class="form-control" ng-model="status">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-custom btn-search btn-block" ng-click="search()">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="filter-box" style="margin-top:10px;">
                <div class="row">
                    <div class="col-md-3"><input type="date" class="form-control" ng-model="dateFrom"></div>
                    <div class="col-md-3"><input type="date" class="form-control" ng-model="dateTo"></div>
                    <div class="col-md-3">
                        <select class="form-control" ng-model="sortBy">
                            <option value="">Sort By</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="price_asc">Price Low to High</option>
                            <option value="price_desc">Price High to Low</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <button class="btn btn-custom btn-add" ng-click="openAddModal()"><i class="fa-solid fa-plus"></i> Add Item</button>
                <button class="btn btn-custom btn-danger-custom" ng-click="openBulkDeleteModal()" ng-disabled="selectedCount() === 0"><i class="fa-solid fa-trash"></i> Bulk Delete (@{{ selectedCount() }})</button>
                <button class="btn btn-custom btn-success-custom" ng-click="openBulkStatusModal()" ng-disabled="selectedCount() === 0"><i class="fa-solid fa-toggle-on"></i> Bulk Status (@{{ selectedCount() }})</button>
                <button class="btn btn-custom btn-export" ng-click="exportCsv()"><i class="fa-solid fa-download"></i> Export CSV</button>
                <span style="margin-left:auto; color:#aaa;">Sorted by: <b>@{{ sortLabel }}</b></span>
            </div>
            <div class="table-box">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell"><input type="checkbox" class="custom-checkbox" ng-change="toggleAll()" ng-model="selectAll"></th>
                            <th ng-click="setSort('id')">ID <span class="sort-icon">@{{ sortDirIcon }}</span></th>
                            <th>Image</th>
                            <th ng-click="setSort('title')">Title</th>
                            <th>Description</th>
                            <th ng-click="setSort('price')">Price <span class="sort-icon">@{{ sortDirIcon }}</span></th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="item in items track by item.id" ng-class="{selected: isSelected(item.id)}">
                            <td class="checkbox-cell">
                                <input type="checkbox" class="custom-checkbox" ng-change="toggleSelect(item.id)" ng-model="selectedItems[item.id]">
                            </td>
                            <td>#@{{ item.id }}</td>
                            <td>
                                <img ng-if="item.image" ng-src="/storage/@{{ item.image }}" class="image-preview" ng-click="viewDetails(item)">
                                <div ng-if="!item.image" class="no-image" ng-click="viewDetails(item)"><i class="fa-solid fa-image"></i></div>
                            </td>
                            <td><b>@{{ item.title }}</b></td>
                            <td>@{{ item.description }}</td>
                            <td>₹ @{{ item.price }}</td>
                            <td><span class="badge-active" style="padding:5px 10px;">@{{ item.category ? item.category.name : 'N/A' }}</span></td>
                            <td>
                                <span ng-if="item.status == 1" class="badge-active">Active</span>
                                <span ng-if="item.status == 0" class="badge-inactive">Inactive</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn view" ng-click="viewDetails(item)" title="View"><i class="fa-solid fa-eye"></i></button>
                                    <button class="action-btn edit" ng-click="openEditModal(item)" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="action-btn delete" ng-click="confirmDelete(item)" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr ng-if="items.length === 0">
                            <td colspan="9" style="text-align:center; padding:40px; color:#aaa;">@{{ loading ? 'Loading...' : 'No items found.' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination-section">
                <button class="page-btn" ng-click="prevPage()" ng-disabled="currentPage == 1 || loading">⬅ Previous</button>
                <div class="page-numbers">
                    <span ng-repeat="p in pageNumbers() track by $index">
                        <button ng-if="p != '...'" class="page-num" ng-click="goToPage(p)" ng-class="{active: p == currentPage}">@{{ p }}</button>
                        <span ng-if="p == '...'" class="page-num" style="cursor:default;">...</span>
                    </span>
                </div>
                <button class="page-btn" ng-click="nextPage()" ng-disabled="currentPage == lastPage || loading">Next ➡</button>
            </div>
        </div>
    </div>
    <div class="loading-overlay" ng-if="loading"><div class="spinner"></div></div>
    <div class="toast-container">
        <div class="toast" ng-repeat="toast in toasts track by $index" ng-class="toast.type" ng-show="toast.show">
            <i class="fa-solid" ng-class="toast.icon"></i> @{{ toast.message }}
        </div>
    </div>
    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="flex:1;">@{{ formTitle }}</h4>
                    <button type="button" class="close close-btn" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form name="itemForm">
                        <div class="form-group"><label>Title</label><input type="text" class="form-control" ng-model="form.title" required></div>
                        <div class="form-group"><label>Description</label><textarea class="form-control" ng-model="form.description" rows="3"></textarea></div>
                        <div class="form-group"><label>Price</label><input type="number" class="form-control" ng-model="form.price" step="0.01" min="0"></div>
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" ng-model="form.category_id">
                                <option value="">Select Category</option>
                                <option ng-repeat="cat in categories" value="@{{ cat.id }}">@{{ cat.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" ng-model="form.status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" class="form-control" accept="image/*" onchange="angular.element(this).scope().uploadImage(this)">
                            <div style="margin-top:10px;" ng-if="form.image">
                                <img ng-src="@{{ form.imagePreview || form.image }}" style="max-height:150px; border-radius:10px;">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" style="background:rgba(255,255,255,0.1); color:white; border:none; border-radius:12px;">Cancel</button>
                    <button class="btn btn-custom btn-add" ng-click="saveItem()" ng-disabled="itemForm.$invalid || loading"><i class="fa-solid fa-save"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm Delete</h4>
                    <button type="button" class="close close-btn" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <b>@{{ deleteItemTitle }}</b>?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" style="background:rgba(255,255,255,0.1); color:white; border:none; border-radius:12px;">Cancel</button>
                    <button class="btn btn-custom btn-danger-custom" ng-click="deleteItemAction()" ng-disabled="loading"><i class="fa-solid fa-trash"></i> Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Item Details</h4>
                    <button type="button" class="close close-btn" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div ng-if="detailItem" style="text-align:center;">
                        <img ng-if="detailItem.image" ng-src="/storage/@{{ detailItem.image }}" class="detail-image" style="margin-bottom:20px;">
                        <div class="detail-row"><span class="detail-label">ID:</span><span class="detail-value">#@{{ detailItem.id }}</span></div>
                        <div class="detail-row"><span class="detail-label">Title:</span><span class="detail-value">@{{ detailItem.title }}</span></div>
                        <div class="detail-row"><span class="detail-label">Description:</span><span class="detail-value">@{{ detailItem.description || 'N/A' }}</span></div>
                        <div class="detail-row"><span class="detail-label">Price:</span><span class="detail-value">₹ @{{ detailItem.price }}</span></div>
                        <div class="detail-row"><span class="detail-label">Category:</span><span class="detail-value">@{{ detailItem.category ? detailItem.category.name : 'N/A' }}</span></div>
                        <div class="detail-row"><span class="detail-label">Status:</span><span class="detail-value">
                            <span ng-if="detailItem.status == 1" class="badge-active">Active</span>
                            <span ng-if="detailItem.status == 0" class="badge-inactive">Inactive</span>
                        </span></div>
                        <div class="detail-row"><span class="detail-label">Created:</span><span class="detail-value">@{{ detailItem.created_at }}</span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" style="background:rgba(255,255,255,0.1); color:white; border:none; border-radius:12px;">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Delete</h4>
                    <button type="button" class="close close-btn" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Delete <b>@{{ selectedCount() }}</b> selected items?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" style="background:rgba(255,255,255,0.1); color:white; border:none; border-radius:12px;">Cancel</button>
                    <button class="btn btn-custom btn-danger-custom" ng-click="bulkDelete()" ng-disabled="loading"><i class="fa-solid fa-trash"></i> Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bulkStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Status Change</h4>
                    <button type="button" class="close close-btn" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Change status for <b>@{{ selectedCount() }}</b> items?</p>
                    <div class="form-group">
                        <label>New Status</label>
                        <select class="form-control" ng-model="bulkStatusValue">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" style="background:rgba(255,255,255,0.1); color:white; border:none; border-radius:12px;">Cancel</button>
                    <button class="btn btn-custom btn-success-custom" ng-click="bulkChangeStatus()" ng-disabled="loading"><i class="fa-solid fa-check"></i> Update</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var app = angular.module('main-App', []);

        app.config(['$httpProvider', function($httpProvider) {
            var token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
            $httpProvider.defaults.headers.common['Accept'] = 'application/json, text/plain, */*';
        }]);

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
    </script>
</body>
</html>
