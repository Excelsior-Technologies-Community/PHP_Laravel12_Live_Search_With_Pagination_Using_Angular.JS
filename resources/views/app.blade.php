<!DOCTYPE html>
<html lang="en" ng-app="main-App">

<head>
    <meta charset="UTF-8">
    <title>Modern Item Dashboard</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AngularJS -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>

    <style>
        * {
            transition: 0.3s;
        }

        body {
            background: linear-gradient(135deg, #141e30, #243b55);
            min-height: 100vh;
            font-family: Arial, sans-serif;
            padding: 30px 0;
            color: white;
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .dashboard-title {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .top-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-box {
            flex: 1;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .stat-box h2 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
        }

        .stat-box p {
            margin-top: 8px;
            font-size: 14px;
        }

        .bg-blue {
            background: linear-gradient(45deg, #2193b0, #6dd5ed);
        }

        .bg-green {
            background: linear-gradient(45deg, #11998e, #38ef7d);
        }

        .bg-red {
            background: linear-gradient(45deg, #cb2d3e, #ef473a);
        }

        .filter-box {
            background: rgba(255, 255, 255, 0.07);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 12px;
            border: none;
            height: 45px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .form-control::placeholder {
            color: #ddd;
        }

        select.form-control option {
            color: black;
        }

        .btn-custom {
            border: none;
            border-radius: 12px;
            height: 45px;
            font-weight: bold;
        }

        .btn-search {
            background: linear-gradient(45deg, #36d1dc, #5b86e5);
            color: white;
        }

        .btn-export {
            background: linear-gradient(45deg, #11998e, #38ef7d);
            color: white;
        }

        .table-box {
            overflow-x: auto;
        }

        .table {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            overflow: hidden;
        }

        .table th {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none !important;
            padding: 15px !important;
        }

        .table td {
            color: #f1f1f1;
            border-color: rgba(255, 255, 255, 0.05) !important;
            padding: 15px !important;
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .badge-active {
            background: #16a34a;
            padding: 7px 15px;
            border-radius: 30px;
            font-size: 12px;
        }

        .badge-inactive {
            background: #dc2626;
            padding: 7px 15px;
            border-radius: 30px;
            font-size: 12px;
        }

        .pagination-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .page-btn {
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .page-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>

</head>

<body ng-controller="ItemController">

    <div class="container">

        <div class="dashboard-card">

            <!-- Title -->
            <div class="dashboard-title">
                <i class="fa-solid fa-box"></i>
                Modern Item Dashboard
            </div>

            <!-- Stats -->
            <div class="top-stats">

                <div class="stat-box bg-blue">
                    <h2>@{{ total }}</h2>
                    <p>Total Items</p>
                </div>

                <div class="stat-box bg-green">
                    <h2>@{{ activeCount }}</h2>
                    <p>Active Items</p>
                </div>

                <div class="stat-box bg-red">
                    <h2>@{{ inactiveCount }}</h2>
                    <p>Inactive Items</p>
                </div>

            </div>

            <!-- Filters -->
            <div class="filter-box">

                <div class="row">

                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="🔍 Search..." ng-model="searchText">
                    </div>

                    <div class="col-md-2">
                        <input type="number" class="form-control" placeholder="Min Price" ng-model="minPrice">
                    </div>

                    <div class="col-md-2">
                        <input type="number" class="form-control" placeholder="Max Price" ng-model="maxPrice">
                    </div>

                    <div class="col-md-2">
                        <select class="form-control" ng-model="status">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select class="form-control" ng-model="sortBy">
                            <option value="">Sort</option>
                            <option value="newest">Newest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-custom btn-search btn-block" ng-click="search()">

                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>

                </div>

            </div>

            <!-- Export -->
            <div class="text-right" style="margin-bottom:20px;">
                <button class="btn btn-custom btn-export" ng-click="exportCsv()">

                    <i class="fa-solid fa-download"></i>
                    Export CSV
                </button>
            </div>

            <!-- Table -->
            <div class="table-box">

                <table class="table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr ng-repeat="item in items">

                            <td>#@{{ item.id }}</td>

                            <td>
                                <b>@{{ item.title }}</b>
                            </td>

                            <td>@{{ item.description }}</td>

                            <td>
                                ₹ @{{ item.price }}
                            </td>

                            <td>

                                <span ng-if="item.status == 1" class="badge-active">

                                    Active
                                </span>

                                <span ng-if="item.status == 0" class="badge-inactive">

                                    Inactive
                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->
            <div class="pagination-section">

                <button class="page-btn" ng-click="prevPage()" ng-disabled="currentPage == 1">

                    ⬅ Previous
                </button>

                <h4>
                    Page @{{ currentPage }}
                    /
                    @{{ lastPage }}
                </h4>

                <button class="page-btn" ng-click="nextPage()" ng-disabled="currentPage == lastPage">

                    Next ➡
                </button>

            </div>

        </div>

    </div>

    <!-- Angular -->
    <script>

        var app = angular.module('main-App', []);

        app.controller('ItemController', function ($scope, $http) {

            $scope.items = [];
            $scope.total = 0;
            $scope.currentPage = 1;
            $scope.lastPage = 1;
            $scope.activeCount = 0;
            $scope.inactiveCount = 0;

            // Load Data
            $scope.search = function (page = 1) {

                $http({

                    method: 'GET',
                    url: '/items',

                    params: {
                        page: page,
                        search: $scope.searchText,
                        min_price: $scope.minPrice,
                        max_price: $scope.maxPrice,
                        status: $scope.status,
                        sort_by: $scope.sortBy
                    }

                }).then(function (response) {

                    $scope.items = response.data.data;
                    $scope.total = response.data.total;
                    $scope.lastPage = response.data.last_page;
                    $scope.currentPage = page;

                    // Count
                    $scope.activeCount = 0;
                    $scope.inactiveCount = 0;

                    angular.forEach($scope.items, function (item) {

                        if (item.status == 1) {
                            $scope.activeCount++;
                        } else {
                            $scope.inactiveCount++;
                        }

                    });

                });

            };

            // Pagination
            $scope.nextPage = function () {

                if ($scope.currentPage < $scope.lastPage) {
                    $scope.search($scope.currentPage + 1);
                }

            };

            $scope.prevPage = function () {

                if ($scope.currentPage > 1) {
                    $scope.search($scope.currentPage - 1);
                }

            };

            // Export CSV
            $scope.exportCsv = function () {
                window.location.href = '/items/export/csv';
            };

            // Initial Load
            $scope.search();

        });

    </script>

</body>

</html>