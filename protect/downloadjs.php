<!doctype html>
<html>
<head>
    <title></title>


    <script src="FileSaver.js" type="text/javascript"></script>
     <script>
        function downloaddd() {
            console.log('will');
            //FileSaver.saveAs("http://18.218.232.156/protect/repository/901/sender.exe", "image.jwerpg");
            //download("http://18.218.232.156/protect/repository/901/sender.exe", "image.jwerpg");

            saveAs("http://18.218.232.156/protect/download/FidMessenger_One_install.exe", "FidMessenger_One_install.exe", {
                onProgress: function(p) {
                    document.title = p.progress + "%, "+p.kbps+" kbps";
                    console.log();
                }
            });

            console.log('ddod');
        }
    </script>


</head>
<body>


    <button type="submit" onclick="downloaddd();">DOWNLOAD</button>
    <a href="http://18.218.232.156/protect/repository/sender_TSI901_install.exe" download="w3logo.exe">sdfsdfsdf</a>

    <!--script src="https://rawgit.com/eligrey/FileSaver.js/master/FileSaver.js" type="text/javascript"></script>
     <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular-animate.js"></script>

    <script type="text/javascript">

        $http({
                url: "http://18.218.232.156/protect/repository/901/sender.exe",
                method: "GET",
                responseType: "blob"
            }).then(function (response) {

        saveAs(response.data,"newfilename.extension");

        });

    </script-->



</body>
</html>

<?php 
  echo 'end';
?>
