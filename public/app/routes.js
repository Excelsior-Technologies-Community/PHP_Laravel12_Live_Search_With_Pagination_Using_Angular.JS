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
