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
