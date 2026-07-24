app.factory('dataFactory', function($http){
    return {
        httpRequest: function(url, method, params, dataPost){
            var pass = { url: url, method: method || 'GET' };
            if(params) pass.params = params;
            if(dataPost) pass.data = dataPost;
            return $http(pass).then(function(r){ return r.data; });
        }
    };
});
