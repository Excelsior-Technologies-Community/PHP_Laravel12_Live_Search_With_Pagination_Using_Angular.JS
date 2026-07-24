var app = angular.module('main-App', []);

app.config(['$httpProvider', function($httpProvider) {
    var token = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = token ? token.getAttribute('content') : '';

    $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    $httpProvider.defaults.headers.common['Accept'] = 'application/json, text/plain, */*';
    $httpProvider.defaults.headers['delete'] = { 'X-CSRF-TOKEN': csrfToken };
    $httpProvider.defaults.headers['put'] = { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' };
}]);
