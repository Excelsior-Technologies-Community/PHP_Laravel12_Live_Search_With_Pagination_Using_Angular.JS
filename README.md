# PHP_Laravel12_Live_Search_With_Pagination_Using_Angular.JS


## Introduction

This project demonstrates how to build a dynamic item management system with live search and pagination using:

Laravel 12 as the backend for database operations

AngularJS as the frontend for dynamic updates without page reloads

Bootstrap 3 for responsive and clean user interface

The main feature of this project is the ability to search items in real-time while navigating large lists with pagination, making it fast and user-friendly.


---


## Key Features

Live Search: Filter items instantly as you type

Pagination: Navigate between pages easily

CRUD Functionality: Add, edit, and delete items via modals

Responsive UI: Works on desktop and mobile screens


---


## Project Structure

```
PHP_Laravel12_Live_Search_With_Pagination_Using_Angular.JS/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ItemController.php
│   ├── Models/
│       └── Item.php
│
├── database/
│   └── migrations/
│       └── 2025_12_29_000000_create_items_table.php
│
├── public/
│   ├── app/
│   │   ├── controllers/
│   │   │   └── ItemController.js
│   │   ├── services/
│   │   │   └── myServices.js
│   │   ├── helper/
│   │   │   └── myHelper.js
│   │   ├── packages/
│   │   │   └── dirPagination.js
│   │   └── routes.js
│   │
│   └── templates/
│        └── items.html
│
│       
│
├── routes/
│   └── web.php
│
└── README.md
```

---


### Step-by-Step Guide

## Step 1: Create a New Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Live_Search_With_Pagination_Using_Angular.JS "12.*"
cd PHP_Laravel12_Live_Search_With_Pagination_Using_Angular.JS
```

---


## Step 2: Configure Database

Update your .env file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=live_search_angular
DB_USERNAME=root
DB_PASSWORD=
```

Run migration for create database table:

```bash
php artisan migrate
```

---


## Step 3: Create items Table Migration

```bash
php artisan make:model Item -m
```

Then update the migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
```

Run migration:

```bash
php artisan migrate
```

---


## Step 4: Define the Model

app/Models/Item.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'price', 'status', 'created_by', 'updated_by'
    ];
}
```

---


## Step 5: Create Controller

```bash
php artisan make:controller ItemController
```

app/Http/Controllers/ItemController.php:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    // GET: Items list with pagination & search
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('price', 'like', "%{$request->search}%")
                  ->orWhere('status', 'like', "%{$request->search}%");
        }

        $items = $query->orderBy('id', 'asc')->paginate(3);

        return response()->json([
            'data' => $items->items(),
            'total' => $items->total(),
            'last_page' => $items->lastPage(),   // <-- Add this
        ]);
    }


    // POST: Create Item
    public function store(Request $request)
    {
        $item = Item::create($request->only([
            'title', 'description', 'price', 'status', 'created_by', 'updated_by'
        ]));
        return response()->json($item);
    }

    // GET: Fetch single item for edit
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return response()->json($item);
    }

    // PUT/PATCH: Update Item
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->update($request->only([
            'title', 'description', 'price', 'status', 'updated_by'
        ]));
        return response()->json($item);
    }

    // DELETE: Remove Item
    public function destroy($id)
    {
        Item::destroy($id);
        return response()->json(['success' => true]);
    }
}
```

---


## Step 6: Setup Routes 

routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', function () {
    return view('app');
});

Route::resource('items', ItemController::class);

// Templates
Route::get('/templates/{template}', function($template){
    return view('templates.' . str_replace('.html', '', $template));
});
```

---


## Step 7: AngularJS Frontend Setup

Create:

public/app/routes.js  
public/app/controllers/ItemController.js  
public/app/services/myServices.js  
public/app/helper/myHelper.js  
public/app/packages/dirPagination.js

1) routes.js
   
```
var app = angular.module('main-App', [
    'ngRoute',
    'angularUtils.directives.dirPagination'
]);

app.config(function($routeProvider){
    $routeProvider
        .when('/items', {
            templateUrl: '/templates/items.html',
            controller: 'ItemController'
        })
        .otherwise({
            redirectTo: '/items'
        });
});
```


2) myServices.js
   
```
app.factory('dataFactory', function($http){
    return {
        httpRequest: function(url, method, params, dataPost){
            var pass = { url: url, method: method || 'GET' };
            if(params) pass.params = params;
            if(dataPost) pass.data = dataPost;
            return $http(pass).then(r => r.data);
        }
    }
});
```


3) myHelper.js

```
function apiModifyTable(originalData, id, response){
    angular.forEach(originalData, function(item, key){
        if(item.id == id){ originalData[key] = response; }
    });
    return originalData;
}
```

4) ItemController.js

```
app.controller('ItemController', ['dataFactory', '$scope', function(dataFactory, $scope) {

    // Status list for select dropdowns
    $scope.statuses = [
        { value: 1, label: 'Active' },
        { value: 0, label: 'Inactive' }
    ];

    $scope.items = [];
    $scope.totalItems = 0;
    $scope.currentPage = 1;
    $scope.lastPage = 1;
    $scope.createForm = {};
    $scope.editForm = {};
    $scope.searchText = '';

    // Load items with pagination & search
    $scope.getResultsPage = function(page) {
        let url = 'items?page=' + page + '&search=' + $scope.searchText;
        dataFactory.httpRequest(url).then(function(response){
            $scope.items = response.data;
            $scope.totalItems = response.total;
            $scope.currentPage = page;
            $scope.lastPage = response.last_page || 1;
        });
    };

    $scope.getResultsPage(1);

    // Watch searchText for live search
    $scope.$watch('searchText', function() {
        $scope.getResultsPage(1);
    });

    // CREATE
    $scope.saveAdd = function() {
        let dataToSend = angular.copy($scope.createForm);
        dataToSend.price = parseFloat(dataToSend.price) || 0;
        dataToSend.status = parseInt(dataToSend.status) || 1;
        dataToSend.created_by = 1; 

        dataFactory.httpRequest('items', 'POST', {}, dataToSend)
            .then(function(response){
                $scope.createForm = {};
                if($scope.addItem){
                    $scope.addItem.$setPristine();
                    $scope.addItem.$setUntouched();
                }
                $("#create-user").modal("hide");
                $scope.getResultsPage($scope.currentPage);
            }).catch(function(err){
                console.log(err);
                alert("Error: Could not save item.");
            });
    };

    // EDIT
    $scope.edit = function(id){
        dataFactory.httpRequest('items/' + id + '/edit')
            .then(function(response){
                response.price = parseFloat(response.price) || 0;
                response.status = parseInt(response.status); // <-- make sure numeric
                $scope.editForm = angular.copy(response);
            });
    };

    // UPDATE
    $scope.saveEdit = function(){
        let data = angular.copy($scope.editForm);
        data._method = 'PUT';
        data.price = parseFloat(data.price) || 0;
        data.status = parseInt(data.status); // <-- numeric
        data.updated_by = 1;

        dataFactory.httpRequest('items/' + data.id, 'POST', {}, data)
            .then(function(response){
                $scope.editForm = {};
                $("#edit-data").modal("hide");
                $scope.getResultsPage($scope.currentPage);
            }).catch(function(err){
                console.log(err);
                alert("Error: Could not update item.");
            });
    };

    // DELETE
    $scope.remove = function(item, index){
        if(confirm("Are you sure you want to delete this item?")){
            dataFactory.httpRequest('items/' + item.id, 'DELETE')
                .then(function(){
                    $scope.getResultsPage($scope.currentPage);
                }).catch(function(err){
                    console.log(err);
                    alert("Error: Could not delete item.");
                });
        }
    };

    // Function to generate page numbers array
    $scope.getPagesArray = function() {
        let pages = [];
        for(let i=1; i<=$scope.lastPage; i++){
            pages.push(i);
        }
        return pages;
    };

}]);



5) dirPagination.js

/*!
 * angular-utils-pagination
 * Version: 0.11.1
 * Author: Michael Bromley
 * License: MIT
 */
(function () {
  'use strict';

  angular.module('angularUtils.directives.dirPagination', [])
    .directive('dirPaginate', ['$parse', function ($parse) {
      return {
        restrict: 'A',
        priority: 1000,
        terminal: true,
        compile: function (element, attrs) {
          var expression = attrs.dirPaginate;
          var match = expression.match(/^\s*(.+)\s+in\s+(.*?)\s*(?:\|\s*itemsPerPage\s*:\s*(\d+))?\s*$/);

          if (!match) {
            throw new Error("Expected expression in form of '_item_ in _collection_ | itemsPerPage:_num_' but got '" +
              expression + "'.");
          }

          var itemString = match[1];
          var collectionString = match[2];
          var itemsPerPage = parseInt(match[3] || '10', 10);

          return function ($scope, $element, $attrs) {
            var collectionGetter = $parse(collectionString);
            var collection = collectionGetter($scope);

            var currentPage = 1;
            var begin = (currentPage - 1) * itemsPerPage;
            var end = begin + itemsPerPage;

            $scope.$watchCollection(collectionString, function (newVal, oldVal) {
              if (!newVal) return;
              var paginatedCollection = newVal.slice(begin, end);
              $element.empty();
              angular.forEach(paginatedCollection, function (item) {
                var clone = $element.clone();
                $scope[itemString] = item;
                $element.after(clone);
              });
            });
          };
        }
      };
    }])
    .directive('dirPaginationControls', function () {
      return {
        restrict: 'E',
        template: '<div class="pagination-container"><ng-transclude></ng-transclude></div>',
        transclude: true
      };
    });

})();
```

---


## Step 8: Angular Views

resources/views/app.blade.php

```
<!DOCTYPE html>
<html lang="en" ng-app="main-App">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel 12 AngularJS CRUD</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>

    <!-- AngularJS -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.2/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.2/angular-route.min.js"></script>


    <!-- Angular Pagination -->
    <script src="{{ asset('app/packages/dirPagination.js') }}"></script>

    <!-- App JS -->
    <script src="{{ asset('/app/routes.js') }}"></script>
    <script src="{{ asset('/app/services/myServices.js') }}"></script>
    <script src="{{ asset('/app/helper/myHelper.js') }}"></script>
    <script src="{{ asset('/app/controllers/ItemController.js') }}"></script>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Laravel 12</a>
            </div>
            <ul class="nav navbar-nav">
                <li><a href="#/items">Items</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <ng-view></ng-view>
    </div>
</body>

</html>
```

---



## Step 8: Create items.html

public/templates/items.html

```
<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="h3 fw-bold text-primary">Item Management</h1>
    </div>

    <!-- Search -->
    <div class="col-md-3 mt-2 mt-md-0">
        <input type="text" class="form-control" placeholder="Search items..." ng-model="searchText" style="height: 40px;">
    </div>

    <!-- Create Button -->
    <div class="col-md-3 d-flex justify-content-md-end mt-2 mt-md-0">
        <button class="btn btn-success" data-toggle="modal" data-target="#create-user" style="height: 40px;">
            <i class="bi bi-plus-lg"></i> Create New
        </button>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Title</th>
            <th>Description</th>
            <th>Price</th>
            <th>Status</th>
            <th width="220px">Action</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="item in items">
            <td>{{ item.id }}</td>
            <td>{{ item.title }}</td>
            <td>{{ item.description }}</td>
            <td>{{ item.price }}</td>
            <td>{{ item.status == 1 ? 'Active' : 'Inactive' }}</td>
            <td>
                <button class="btn btn-primary" ng-click="edit(item.id)" data-toggle="modal" data-target="#edit-data">Edit</button>
                <button class="btn btn-danger" ng-click="remove(item, $index)">Delete</button>
            </td>
        </tr>
    </tbody>
</table>

<!-- Centered Pagination for Bootstrap 3 -->
<nav aria-label="Page navigation" class="text-center">
    <ul class="pagination">
        <!-- Previous -->
        <li ng-class="{disabled: currentPage == 1}">
            <a href="" ng-click="getResultsPage(currentPage - 1)">Previous</a>
        </li>

        <!-- Page Numbers -->
        <li ng-repeat="page in getPagesArray()" ng-class="{active: currentPage == page}">
            <a href="" ng-click="getResultsPage(page)">{{ page }}</a>
        </li>

        <!-- Next -->
        <li ng-class="{disabled: currentPage == lastPage}">
            <a href="" ng-click="getResultsPage(currentPage + 1)">Next</a>
        </li>
    </ul>
</nav>


<!-- CREATE MODAL -->
<div class="modal fade" id="create-user" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form name="addItem" ng-submit="saveAdd()">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Item</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Title:</strong>
                        <input type="text" ng-model="createForm.title" class="form-control" placeholder="Title" required>
                    </div>
                    <div class="form-group">
                        <strong>Description:</strong>
                        <textarea ng-model="createForm.description" class="form-control" placeholder="Description"></textarea>
                    </div>
                    <div class="form-group">
                        <strong>Price:</strong>
                        <input type="number" ng-model="createForm.price" class="form-control" placeholder="Price">
                    </div>
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select ng-model="createForm.status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <!-- Optional hidden fields for created_by -->
                    <input type="hidden" ng-model="createForm.created_by" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" ng-disabled="addItem.$invalid" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="edit-data" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form name="editItem" ng-submit="saveEdit()">
                <input type="hidden" ng-model="editForm.id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Item</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Title:</strong>
                        <input type="text" ng-model="editForm.title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <strong>Description:</strong>
                        <textarea ng-model="editForm.description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <strong>Price:</strong>
                        <input type="number" ng-model="editForm.price" class="form-control">
                    </div>
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select ng-model="editForm.status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <!-- Optional hidden field for updated_by -->
                    <input type="hidden" ng-model="editForm.updated_by" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" ng-disabled="editItem.$invalid" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
```

---


## Step 9: Run the App

```bash
php artisan serve
```

Visit 

```bash
http://127.0.0.1:8000
```


---

## Output

**Live Search With Pagination**

<img width="1919" height="1036" alt="Screenshot 2025-12-30 162738" src="https://github.com/user-attachments/assets/151c507f-f316-4c6b-b577-58feb44c9672" />

<img width="1919" height="1027" alt="Screenshot 2025-12-30 162607" src="https://github.com/user-attachments/assets/a425d61f-ef91-4cd5-83d4-230adf08a0c6" />


**Create Item**

<img width="1919" height="1029" alt="Screenshot 2025-12-30 162553" src="https://github.com/user-attachments/assets/23d70518-03ed-4acd-a218-39d6a1065566" />

<img width="1919" height="1027" alt="Screenshot 2025-12-30 162607" src="https://github.com/user-attachments/assets/7dd906c1-6fb4-47de-9194-c8e0b52c40e6" />


**Edit Item**

<img width="1919" height="1027" alt="Screenshot 2025-12-30 162636" src="https://github.com/user-attachments/assets/142fbf83-b93a-4171-9608-6c5ecbfdaff0" />

<img width="1918" height="1027" alt="Screenshot 2025-12-30 162646" src="https://github.com/user-attachments/assets/af6fd556-ac8c-410e-af13-3665ae6f149e" />


**Delete Item**

<img width="1919" height="1025" alt="Screenshot 2025-12-30 162702" src="https://github.com/user-attachments/assets/eef2a223-ea68-4a86-824c-c6c368da9f8c" />

<img width="1919" height="1027" alt="Screenshot 2025-12-30 162712" src="https://github.com/user-attachments/assets/0d32ed42-1038-4fea-9de2-d6a2575da42a" />


---

Your PHP_Laravel12_Live_Search_With_Pagination_Using_Angular.JS Project is Now Ready!
<<<<<<< HEAD
=======

>>>>>>> development
