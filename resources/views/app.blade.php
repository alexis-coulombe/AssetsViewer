<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Interactive Chart, analyze all the data with a huge range of indicators.">

        <title>Laravel</title>
        <link rel="stylesheet" type="text/css" href="/css/app.css?v={{ filemtime(public_path() . '/css/app.css') }}">
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    </head>
    <body class="bg-black-alt font-sans leading-normal tracking-normal">

        <div id="app">
            <assets-navbar></assets-navbar>
            <div class="container w-full mx-auto pt-20">
                <router-view></router-view>
            </div>
            <assets-footer></assets-footer>
        </div>

        <script src="/js/app.js?v={{ filemtime(public_path() . '/js/app.js') }}"></script>

        <script>
          /*Toggle dropdown list*/
          /*https://gist.github.com/slavapas/593e8e50cf4cc16ac972afcbad4f70c8*/

          var userMenuDiv = document.getElementById("userMenu");
          var userMenu = document.getElementById("userButton");

          var navMenuDiv = document.getElementById("nav-content");
          var navMenu = document.getElementById("nav-toggle");

          document.onclick = check;

          function check(e){
            var target = (e && e.target) || (event && event.srcElement);

            //User Menu
            if (!checkParent(target, userMenuDiv)) {
              // click NOT on the menu
              if (checkParent(target, userMenu)) {
                // click on the link
                if (userMenuDiv.classList.contains("invisible")) {
                  userMenuDiv.classList.remove("invisible");
                } else {userMenuDiv.classList.add("invisible");}
              } else {
                // click both outside link and outside menu, hide menu
                userMenuDiv.classList.add("invisible");
              }
            }

            //Nav Menu
            if (!checkParent(target, navMenuDiv)) {
              // click NOT on the menu
              if (checkParent(target, navMenu)) {
                // click on the link
                if (navMenuDiv.classList.contains("hidden")) {
                  navMenuDiv.classList.remove("hidden");
                } else {navMenuDiv.classList.add("hidden");}
              } else {
                // click both outside link and outside menu, hide menu
                navMenuDiv.classList.add("hidden");
              }
            }

          }

          function checkParent(t, elm) {
            while(t.parentNode) {
              if( t == elm ) {return true;}
              t = t.parentNode;
            }
            return false;
          }


        </script>

    </body>
</html>
