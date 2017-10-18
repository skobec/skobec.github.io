var map;
var response;
var reg = 'Нижегородская';
var filterElem = [];
function initMap(id) {
  $.getJSON('js/j.json', function(res) {
    response = res;
    createList();
    var uluru;
    var d;
    if (id) {
      d = res.filter(function(s, i) {
        if (id === i) {

        }
        return false;
      });
      uluru = { lat: d[0].coor[0], lng:  d[0].coor[1] };
    } else {
      uluru = {lat: 54.7818, lng:  32.0401};
    }
    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 11,
      center: uluru,
    });
    var icon = {
        url: "img/greymarker.svg",
    };
    for (var i = 0; i < res.length; i++) {
      var marker = new google.maps.Marker({
        position: { lat: res[i].coor[0], lng: res[i].coor[1] },
        map: map,
        icon: icon,
      });
    }
  });

}

function createList() {
  var data = response.filter(function (s) {
    if (s.address.indexOf(reg) > -1) {
      return true;
    }
    return false;
  });
  if (filterElem.length > 0) {
    filterElem.map(function (s) {
      data = data.filter(function (m) {
        console.log(m);
        if (m.filter.indexOf(s) > -1) {
          return true;
        }
        return false;
      });
      return s;
    });
    console.log(filterElem);
    console.log(data);
  }
  var list = document.getElementById('accordion');
  list.innerHTML = '';
  if (data.length === 0) {
    list.innerHTML = '<span>АЗС подходящие Вашим кретериям скоро появятся!</span>';
    return;
  }
  for (var i = 0; i < data.length; i += 1) {
    var div = document.createElement('div');
    div.className = "card";
    var show = i === 0 ? '' : '';
    div.innerHTML = '<div  data-index="' + data[i].id +'" class="card-header" role="tab" id="headingOne'+ i +'">'+
                    '<a class="links-row collapsed"  data-index="' + data[i].id +'" data-toggle="collapse" href="#collapseOne'+ i +'" aria-expanded="true" aria-controls="collapseOne'+ i +'">'+
                    '<span class="row"  data-index="' + data[i].id +'">'+
                    '<span class="col-xl-5 name"  data-index="' + data[i].id +'">'+ data[i].name +'</span>'+
                    '<span class="col-xl-7 adress" data-index="' + data[i].id +'">'+ data[i].address +'</span>'+
                    '</span></a></div>'+
                    '<div data-index="' + data[i].id +'" id="collapseOne'+ i +'" class="collapse '+ show +'" role="tabpanel" aria-labelledby="headingOne'+ i +'" data-parent="#accordion">'+
                    '<div class="row card-body" data-index="' + data[i].id +'">'+
                    '<div class="col-xl-5" data-index="' + data[i].id +'">'+
                    '<img style="width: 70%;" src="/img/azs-list-img.jpg" data-index="' + data[i].id +'">'+
                    '</div><div class="col-xl-7 podinfo-azs" data-index="' + data[i].id +'">'+
                    '<p data-index="' + data[i].id +'">'+ data[i].grafik + ' ' + data[i].oil.join(', ') +'</p>'+
                    '<p data-index="' + data[i].id +'">Координаты: '+ data[i].coor[0] + ', ' + data[i].coor[1] +'</p>'+
                    '<p data-index="' + data[i].id +'">'+ data[i].tel +'</p>'+
                    '</div></div></div>';
    list.appendChild(div);
  }
  function rebuildMap(id) {
    if (id) {
      d = response.filter(function(s, i) {
        if (+id === s.id) {
          return true;
        }
        return false;
      });
      uluru = { lat: d[0].coor[0], lng:  d[0].coor[1] };
    } else {
      uluru = {lat: 54.7818, lng:  32.0401};
    }
    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 11,
      center: uluru,
    });
    var icon = {
        url: "img/redmarker.svg",
    };
    var icon2 = {
        url: "img/greymarker.svg",
    };
    for (var i = 0; i < response.length; i++) {
      var marker = new google.maps.Marker({
        position: { lat: response[i].coor[0], lng: response[i].coor[1] },
        map: map,
        icon: +id === response[i].id ? icon : icon2,
      });
    }
  }
  $('.card').click(function (event) {
    rebuildMap(event.target.getAttribute('data-index'))
  });
}

$( document ).ready(function() {
    $('.labelClick').click(function (event) {
      reg = event.target.getAttribute('data-reg');
      createList();
    });
    $('.azsButton').click(function (event) {
      if (filterElem.indexOf(event.target.getAttribute('data-type')) < 0) {
        filterElem.push(event.target.getAttribute('data-type'));
      } else {
        filterElem.splice(filterElem.indexOf(event.target.getAttribute('data-type')) ,1);
      }
      if (filterElem.length === 0) {
        document.getElementsByClassName('azsButtonAll')[0].classList.add('active');
      } else {
        document.getElementsByClassName('azsButtonAll')[0].classList.remove('active');
      }
      createList();
    });
    $('.azsButtonAll').click(function (event) {
      $('.azsButton').removeClass('active');
      // document.getElementsByClassName('azsButtonAll')[0].classList.add('active');
      filterElem = [];
      createList();
    });
});
