var app = angular.module('ServerInfoApp', []);

app.config(($compileProvider) => {
    // add steam:// and minecraft:// links to the list of allowed protocols
    $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|s?ftp|mailto|tel|file|steam|minecraft|mtasa):/);
});

app.controller('BackgroundImageCtrl', ($scope, $interval) => {
    // list of images to cycle through in the page's background
    $scope.imgs = [
        'img/valve/ss_7a571ed8e766ab8cf9913c1000a3dbf9308550b7.jpg',
        'img/valve/ss_09a55c755073d11c5c708da922abd9005546a5ee.jpg',
        'img/valve/ss_29cdd791483f8facbe7daa8f1b59b55b386d24c1.jpg',
        'img/valve/ss_65b464dd068f4a256e0bfbf9110788554d28aaa2.jpg',
        'img/valve/ss_679d0036062b254569a78966183dec599069fead.jpg',
        'img/valve/ss_a9b7bb493ac15b4b5d6dc7b81b2a80d3923de441.jpg',
        'img/hl2mp/ss_0312ff64a5839736a7dfa4c4f735f8d48843739e.jpg',
        'img/hl2mp/ss_57349aec96338ccabd38577ea8fcf80b42f0ef49.jpg',
        'img/hl2mp/ss_7241594c2575bc1f822f1431490cea485b7aef8a.jpg',
        'img/hl2mp/ss_aee760995a2913908a7d4d0471ca81a22232f4a6.jpg',
    ];

    // on a 10 second interval, cycle to the next image (or back the first if there aren't more)
    $scope.activeImg = 0;
    $interval(() => $scope.activeImg = $scope.activeImg + 1 >= $scope.imgs.length ? 0 : $scope.activeImg + 1, 10000);
});

app.controller('ServerListCtrl', ($scope, $http, $timeout) => {
    // make angularJS's functions available in the template (i.e. angular.isDefined)
    $scope.angular = angular;

    // initialize an empty list of servers for the ui
    $scope.servers = [];
    
    // define and call a function to refresh the server list
    $scope.refresh = function () {
        $scope.errorMessage = null;
        $scope.loadingServers = true;
        $http.get('status.php')
        .then((response) => {
            // overwrite the list
            // maybe it'd be better to update the values rather than replacing them to avoid the DOM items being recreated
            $scope.servers = response.data;
            // repeat this after 3 seconds
            $timeout($scope.refresh, 3000);
        }, (response) => {
            // TODO: better error handling
            $scope.servers.length = 0;
            var type = response.headers('Content-Type');
            if((type === 'application/json' || type === 'text/plain') && response.data !== null) {
                $scope.errorMessage = response.data;
            } else {
                $scope.errorMessage = '';
            }
            console.log(response);
        })
        .finally(() => {
            $scope.loadingServers = false;
        })
    }
    $scope.refresh();
});