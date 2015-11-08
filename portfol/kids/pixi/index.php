<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Большое путешествие</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script src="/pixi/pixi.min.js"></script>
        <script src="/pixi/pixi.dom.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
        <script src="/pixi/game.js"></script>
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/owl.carousel.css" rel="stylesheet">
        <link href="../css/style.css" rel="stylesheet">
        <link href="../css/my.css" rel="stylesheet">
        <!-- https://github.com/kittykatattack/learningPixi -->
        <style>
            html, body {
                padding: 0;
                margin: 0;
                height: 100%;
            }
            body {
                background: url(background.jpg);
                background-size: cover;
                transform: none;
            }
            #game {
                margin: 0 auto;
                text-align: center;

            }
            #game > div {
                margin: 0 auto;
            }
            #popup_bad {
                width: 500px;
            }
            #popup_bad, .modal-backdrop {
                display: none;
            }
            .info_window {
                width: 500px;
                height: auto;
            }
            .info_window_inner {
                height: auto;
                text-align: center;
            }
            #popup_bad .btn_maps {
                float: none;
                margin: 0 auto;
                text-align: center;
                width: 150px;
            }
            @media only screen and (max-width: 1366px) {
                body {
                    zoom: 1;
                    -moz-transform: scale(1);
                    -ms-zoom: 1;
                }
            }
        </style>
    </head>
    <body>

        <div id="game"></div>

        <script>
            var map;
            function view_popup() {
                var el = $('#popup_bad');
                $('body > .modal-backdrop').show();
                el.show();
                var w = $(window);
                el.css("z-index", "2000");
                el.css("top", (w.height() - el.height()) / 2 + "px");
                el.css("left", (w.width() - el.width()) / 2 + "px");
            }
            $(function () {
                $('.popup_close').on('click', function () {
                    $('body > .modal-backdrop').hide();
                    $('#popup_bad').hide();
                });
                $('.popup_close').on('touchstart', function () {
                    $('body > .modal-backdrop').hide();
                    $('#popup_bad').hide();
                });
                var width = window.innerWidth;
                var height = window.innerHeight;
                if (width > 1400)
                    width = 1400;
                if (height > 768)
                    height = 768;
                game.init('game', width, height);

                // Видео
                /*var video = new PIXI.Sprite(PIXI.Texture.fromVideo('testVideo.mp4'));
                 video.width = 200;
                 video.height = 145;*/

                // Группа объектов
                map = game.addGroup([
                    PIXI.Sprite.fromImage('map_deti_2.png'),
                    /*game.createObject(PIXI.extras.MovieClip.fromImages(['opened.png', 'opened2.png']), 200, 100, true, false, .5, .5).on('click', function(e) {alert('Opened');}),
                     /*game.createObject(PIXI.extras.MovieClip.fromImages(['lock.png', 'lock2.png']), 330, 250, true, false, .5, .5).on('click', function(e) {alert('Locked');}),*/
                    game.createObject(PIXI.extras.MovieClip.fromImages(['success.png', 'success.png']), 110, 581, true, false, .5, .5)
                            .on('touchstart', function (e) {
                                window.location.href = '/';
                            })
                            .on('click', function (e) {
                                window.location.href = '/';
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 265, 450, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('touchstart click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 330, 520, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 392, 631, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 550, 591, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 485, 465, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 745, 485, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 750, 610, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 955, 665, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 930, 480, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 1025, 345, true, false, .5, .5)
                            .on('touchstart', function (e) { /*window.location.href = '/';*/
                            })
                            .on('click', function (e) { /*window.location.href = '/';*/
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['block.png', 'block.png']), 1285, 585, true, false, .5, .5)
                            .on('touchstart', function (e) {
                                view_popup();
                            })
                            .on('click', function (e) {
                                view_popup();
                            }),
                            //game.createObject(video, 150, 680, false, false),
                            //game.createObject(PIXI.Sprite.fromImage('tv.png'), 150, 550, true, false, 0, 0),
                ], 0, 0, true);
                game.cameraUpdate(map);

                // Самолет (генератор полигонов http://cssplant.com/clip-path-generator)
                /*var airplane = PIXI.extras.MovieClip.fromImages(['airplane.png', 'airplane2.png']);
                 airplane.hitArea = new PIXI.Polygon([
                 new PIXI.Point(21, 72),
                 new PIXI.Point(9, 95),
                 new PIXI.Point(8, 133),
                 new PIXI.Point(22, 164),
                 new PIXI.Point(67, 193),
                 new PIXI.Point(111, 205),
                 new PIXI.Point(117, 236),
                 new PIXI.Point(141, 243),
                 new PIXI.Point(174, 240),
                 new PIXI.Point(199, 253),
                 new PIXI.Point(243, 254),
                 new PIXI.Point(259, 248),
                 new PIXI.Point(261, 235),
                 new PIXI.Point(233, 202),
                 new PIXI.Point(244, 193),
                 new PIXI.Point(284, 178),
                 new PIXI.Point(315, 160),
                 new PIXI.Point(338, 171),
                 new PIXI.Point(349, 170),
                 new PIXI.Point(360, 164),
                 new PIXI.Point(346, 131),
                 new PIXI.Point(348, 116),
                 new PIXI.Point(339, 102),
                 new PIXI.Point(353, 20),
                 new PIXI.Point(348, 9),
                 new PIXI.Point(326, 6),
                 new PIXI.Point(311, 11),
                 new PIXI.Point(275, 76),
                 new PIXI.Point(195, 41),
                 new PIXI.Point(152, 16),
                 new PIXI.Point(131, 8),
                 new PIXI.Point(105, 8),
                 new PIXI.Point(71, 12),
                 new PIXI.Point(59, 21),
                 new PIXI.Point(43, 56)
                 ]); 
                 game.addObject(airplane, game.width / 2, game.height/ 2, true, true, 0, 0);*/

                // Группа объектов
                game.addGroup([
                    game.createObject(PIXI.extras.MovieClip.fromImages(['zoom-minus.png', 'zoom-minus.png']), 0, game.height - 80, true, false, .5, .5)
                            .on('touchstart', function (e) {
                                delta = -0.05;
                                if (mapScaleTarget + delta > 0.95 && mapScaleTarget + delta < 4.0)
                                    mapScaleTarget += delta;
                                game.cameraUpdate(map, e, delta);
                            })
                            .on('click', function (e) {
                                delta = -0.05;
                                if (mapScaleTarget + delta > 0.95 && mapScaleTarget + delta < 4.0)
                                    mapScaleTarget += delta;
                                game.cameraUpdate(map, e, delta);
                            }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['zoom-plus.png', 'zoom-plus.png']), 50, game.height - 80, true, false, .5, .5)
                            /*if(map.scale.x < 2) {
                             map.scale.x = map.scale.x + 0.2;
                             // map.position.x = (map.position.x/2 + 512) - map.scale.x * 1024 / 2;
                             }
                             if(map.scale.y < 2) {
                             map.scale.y = map.scale.y + 0.2;
                             // map.position.y = (map.position.y/2 + 384) - map.scale.y * 768 / 2;
                             }*/
                            .on('click', function (e) {
                                delta = 0.05;
                                if (mapScaleTarget + delta > 0.95 && mapScaleTarget + delta < 4.0)
                                    mapScaleTarget += delta;
                                game.cameraUpdate(map, e, delta);
                            })
                            .on('touchstart', function (e) {
                                delta = 0.05;
                                if (mapScaleTarget + delta > 0.95 && mapScaleTarget + delta < 4.0)
                                    mapScaleTarget += delta;
                                game.cameraUpdate(map, e, delta);
                            }),
                ], 40, 40, false);

                // Группа объектов
                game.addGroup([
                    game.createObject(PIXI.extras.MovieClip.fromImages(['header.png']), game.width - 430, 20, true, false, .5, .5).on('click', function (e) {
                    }),
                    game.createObject(PIXI.extras.MovieClip.fromImages(['logo.png']), 90, 20, true, false, .5, .5).on('click', function (e) {
                    }),
                ], 40, 40, false);

                // Текст
                // game.addText('40 pts', game.width - 150, 40);
                draw();

            });
        </script>

        <!-- popup -->
        <div class="popup" id="popup_bad">
            <div class="info_window" id="info_window">
                <div class="info_window_inner has_header">
                    <h1>Ой :(</h1>
                    <p style="margin-bottom: 10px;">Молодец! Ты правильно ответил на все вопросы и получаешь за это награду - Радиотехника!</p>
                    <div class="name" style="margin-top: 10px; overflow: hidden;">
                        <a href="main.html" class="btn btn_maps"><span>На карту</span></a>
                    </div>
                </div>
                <a href="javascript:void(0);" class="popup_close" data-rel="info_window"></a>
            </div>
        </div>
        <div class="modal-backdrop in"></div>
    </body>
</html>