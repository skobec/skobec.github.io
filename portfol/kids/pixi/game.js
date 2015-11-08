function getBodyScrollTop() {
	return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
}
function getBodyScrollLeft() {
	return self.pageXOffset || (document.documentElement && document.documentElement.scrollLeft) || (document.body && document.body.scrollLeft);
}
function getClientWidth() {
	return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
}
function getClientHeight() {
	return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
}
var renderer;
var stage;
var mapScaleTarget = 1.0;
var zoomSpeed = 0.05;
var globalMousePosition;
var pixiWindow;
function draw() {
	/*rendererResize();
	init();*/
	requestAnimationFrame(frameUpdate);
}
function frameUpdate() {
	requestAnimationFrame(frameUpdate);
	renderer.render(stage);
}

var game = {
    
    width: 0,
    height: 0,

	init: function(container_id, width, height) {
        this.width = width;
        this.height = height;
		renderer = PIXI.autoDetectRenderer(width, height);
        $('#' + container_id)
        document.getElementById(container_id).appendChild(renderer.view);
		/*document.body.appendChild(renderer.view);*/
		PIXI.DOM.Setup( renderer, true );
		stage = new PIXI.Stage(0x97c56e, true);
		
		$('#' + container_id).find('canvas').mousewheel(function(event) {
			var delta = event.deltaY / 5.0;
			if(delta > 0) {
				delta = zoomSpeed;
			}else{
				delta = -zoomSpeed;
			}
			if (mapScaleTarget + delta > 0.95 && mapScaleTarget + delta < 4.0)
				mapScaleTarget += delta;
			globalMousePosition = {x: event.deltaX, y: event.deltaY};
			game.cameraUpdate(map, event, delta);
		});
		$('#' + container_id).find('canvas').on('touchmove', function(event) {
			if (event.originalEvent.touches.length === 2) {
				var delta = event.originalEvent.touches[0].pageY / 5.0;
				if(delta > 0) {
					delta = zoomSpeed;
				}else{
					delta = -zoomSpeed;
				}
				if (mapScaleTarget + delta > 0.95 && mapScaleTarget + delta < 4.0)
					mapScaleTarget += delta;
				globalMousePosition = {x: event.originalEvent.touches[0].pageX, y: event.originalEvent.touches[0].pageY};
				game.cameraUpdate(map, event, delta);
			}
		})
	},
	//call this every frame
	cameraUpdate:function(map, event, delta) {
		//scale map
		if (Math.abs(mapScaleTarget - map.scale.x) >= 0.05) {
			localMousePosition = map.toLocal(globalMousePosition);

			if (delta < 0) {
				map.scale.x -= zoomSpeed;
				map.scale.y -= zoomSpeed;
			} else {
				map.scale.x += zoomSpeed;
				map.scale.y += zoomSpeed;
			}

			map.position.x = -(localMousePosition.x * map.scale.x) + globalMousePosition.x;
			map.position.y = -(localMousePosition.y * map.scale.x) + globalMousePosition.y;
		}
	},
	addGroup: function(sprites, x, y, dragging) {
		// create the stuffs
		var group1 = new PIXI.Container();
		// add the sprites
		$.each(sprites, function(k, v){
			group1.addChild(v);
		});
		this.addObject(group1, x, y, false, dragging, 0, 0);
		return group1;
	},

	/**
	* Создание объекта
	*/
	createObject: function(sprite, x, y, hover, dragging, cx, cy) {

		// центр координат объекта
		console.log(sprite);
		if(typeof sprite.anchor != 'undefined') {
			sprite.anchor.set(cx, cy);
		}
		sprite.position.x = x;
		sprite.position.y = y;

		if(hover || dragging) {
			// make the interactive...
			sprite.interactive = true;
			sprite.buttonMode = true;
		}

		// смена спрайта при наведении
		if(hover) {
			sprite.mouseover = function(event) {
			    this.gotoAndStop(1);
			};
			sprite.mouseout = function(event) {
			    this.gotoAndStop(0);
			};
		}

		// таскаемый
		if(dragging) {
			sprite.mousedown = sprite.touchstart = function(event) {
			    this.data = event.data;
			    this.dragging = true;
				this.sx = this.data.getLocalPosition(sprite).x * sprite.scale.x;
			    this.sy = this.data.getLocalPosition(sprite).y * sprite.scale.y;
			};
			// set the events for when the mouse is released or a touch is released
			sprite.mouseup = sprite.mouseupoutside = sprite.touchend = sprite.touchendoutside = function(event) {
			    this.dragging = false;
			    // set the interaction data to null
			    this.data = null;
			};
			// set the callbacks for when the mouse or a touch moves
			sprite.mousemove = sprite.touchmove = function(event) {
			    if(this.dragging) {
			        // need to get parent coords..
			        var newPosition = this.data.getLocalPosition(this.parent);
			        globalMousePosition = this.data.getLocalPosition(this.parent);
			        var x = newPosition.x - this.sx;
			        var y = newPosition.y - this.sy;
			        if(this.width < renderer.width || this.height < renderer.height) {
			            x = Math.max(x, 0);
			            y = Math.max(y, 0);
			            x = Math.min(x, renderer.width - this.width);
			            y = Math.min(y, renderer.height - this.height);
			        } else {
			            x = Math.min(x, 0);
			            y = Math.min(y, 0);
						x = Math.max(x, renderer.width - this.width);
						y = Math.max(y, renderer.height - this.height);
			        }
			        this.position.x = x;
			        this.position.y = y;
			    }
			}
		}

		return sprite;

	},

	/**
	* Добавление объекта на сцену
	*/
	addObject: function(sprite, x, y, hover, dragging) {
		stage.addChild(this.createObject(sprite, x, y, hover, dragging));
	},
	
	addText: function(text, x, y) {
		var style = {
		    font : 'bold italic 36px Arial',
		    fill : '#F7EDCA',
		    stroke : '#4a1850',
		    strokeThickness : 5,
		    dropShadow : true,
		    dropShadowColor : '#000000',
		    dropShadowAngle : Math.PI / 6,
		    dropShadowDistance : 6,
		    wordWrap : true,
		    wordWrapWidth : 440
		};
		var richText = new PIXI.Text(text,style);
		this.addObject(richText, x, y, false, false);
	},

};
