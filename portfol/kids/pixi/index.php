<!doctype html>
<html>
<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script src="/pixi/pixi.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
	<script src="/pixi/game.js"></script>
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
        }
        #game {
            margin: auto auto;
            text-align: center;
            
        }
        #game > * {
            border: 4px solid #fff;
            margin-top: 50px;
            border-radius: 10px;
            box-shadow: 20px 20px 40px rgba(0,0,0,.5);
        }
	</style>
</head>
<body>

    <div id="game"></div>

	<script>
	
		$(function(){

			game.init('game', 1024, 768);

            // Видео
            /*var video = new PIXI.Sprite(PIXI.Texture.fromVideo('testVideo.mp4'));
            video.width = 200;
            video.height = 145;*/

			// Группа объектов
			var map = game.addGroup([
				PIXI.Sprite.fromImage('game-map.jpg'),
				/*game.createObject(PIXI.extras.MovieClip.fromImages(['opened.png', 'opened2.png']), 200, 100, true, false, .5, .5).on('click', function(e) {alert('Opened');}),
				game.createObject(PIXI.extras.MovieClip.fromImages(['lock.png', 'lock2.png']), 330, 250, true, false, .5, .5).on('click', function(e) {alert('Locked');}),
				game.createObject(PIXI.extras.MovieClip.fromImages(['lock.png', 'lock2.png']), 500, 150, true, false, .5, .5).on('click', function(e) {alert('Locked');}),*/
                //game.createObject(video, 150, 680, false, false),
                //game.createObject(PIXI.Sprite.fromImage('tv.png'), 150, 550, true, false, 0, 0),
			], 0, 0, true);

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
				game.createObject(PIXI.extras.MovieClip.fromImages(['zoom-minus.png', 'zoom-minus.png']), 0, game.height-80, true, false, .5, .5).on('click', function(e) {
					if(map.scale.x > 1) {
						map.scale.x = map.scale.x - 0.1;
					}
					if(map.scale.y > 1) {
						map.scale.y = map.scale.y - 0.1;
					}
					console.log(map.sx+' - '+map.sy);
					console.log(game.height);
				}),
				game.createObject(PIXI.extras.MovieClip.fromImages(['zoom-plus.png', 'zoom-plus.png']), 50, game.height-80, true, false, .5, .5).on('click', function(e) {
					if(map.scale.x < 2) {
						map.scale.x = map.scale.x + 0.1;
					}
					if(map.scale.y < 2) {
						map.scale.y = map.scale.y + 0.1;
					}
				}),
			], 40, 40, false);

			// Группа объектов
			game.addGroup([
				game.createObject(PIXI.extras.MovieClip.fromImages(['header.png']), game.width - 430, 20, true, false, .5, .5).on('click', function(e) {}),
				game.createObject(PIXI.extras.MovieClip.fromImages(['logo.png']), 90, 20, true, false, .5, .5).on('click', function(e) {alert('Zoom--');}),
			], 40, 40, false);

            // Текст
			// game.addText('40 pts', game.width - 150, 40);

			draw();

		});
	</script>
</body>
</html>