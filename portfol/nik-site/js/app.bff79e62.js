(function(s){function t(t){for(var i,c,_=t[0],o=t[1],n=t[2],d=0,v=[];d<_.length;d++)c=_[d],Object.prototype.hasOwnProperty.call(e,c)&&e[c]&&v.push(e[c][0]),e[c]=0;for(i in o)Object.prototype.hasOwnProperty.call(o,i)&&(s[i]=o[i]);r&&r(t);while(v.length)v.shift()();return l.push.apply(l,n||[]),a()}function a(){for(var s,t=0;t<l.length;t++){for(var a=l[t],i=!0,_=1;_<a.length;_++){var o=a[_];0!==e[o]&&(i=!1)}i&&(l.splice(t--,1),s=c(c.s=a[0]))}return s}var i={},e={app:0},l=[];function c(t){if(i[t])return i[t].exports;var a=i[t]={i:t,l:!1,exports:{}};return s[t].call(a.exports,a,a.exports,c),a.l=!0,a.exports}c.m=s,c.c=i,c.d=function(s,t,a){c.o(s,t)||Object.defineProperty(s,t,{enumerable:!0,get:a})},c.r=function(s){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(s,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(s,"__esModule",{value:!0})},c.t=function(s,t){if(1&t&&(s=c(s)),8&t)return s;if(4&t&&"object"===typeof s&&s&&s.__esModule)return s;var a=Object.create(null);if(c.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:s}),2&t&&"string"!=typeof s)for(var i in s)c.d(a,i,function(t){return s[t]}.bind(null,i));return a},c.n=function(s){var t=s&&s.__esModule?function(){return s["default"]}:function(){return s};return c.d(t,"a",t),t},c.o=function(s,t){return Object.prototype.hasOwnProperty.call(s,t)},c.p="/nik-site/";var _=window["webpackJsonp"]=window["webpackJsonp"]||[],o=_.push.bind(_);_.push=t,_=_.slice();for(var n=0;n<_.length;n++)t(_[n]);var r=o;l.push([0,"chunk-vendors"]),a()})({0:function(s,t,a){s.exports=a("56d7")},"034f":function(s,t,a){"use strict";a("85ec")},"041d":function(s,t,a){},"12dc":function(s,t,a){"use strict";a("8ef7")},"14b0":function(s,t,a){},"2ceb":function(s,t,a){"use strict";a("7247")},"394f":function(s,t,a){s.exports=a.p+"img/mobile-image.5173bbc2.png"},4119:function(s,t,a){},"459e":function(s,t,a){},"53c3":function(s,t,a){s.exports=a.p+"img/insta.74f4a3e1.svg"},"56d7":function(s,t,a){"use strict";a.r(t);a("e260"),a("e6cf"),a("cca6"),a("a79d");var i=a("2b0e"),e=function(){var s=this,t=s.$createElement,a=s._self._c||t;return a("div",{attrs:{id:"app"}},[a("router-view")],1)},l=[],c=(a("034f"),a("2877")),_={},o=Object(c["a"])(_,e,l,!1,null,null,null),n=o.exports,r=a("9483");Object(r["a"])("".concat("/nik-site/","service-worker.js"),{ready:function(){console.log("App is being served from cache by a service worker.\nFor more details, visit https://goo.gl/AFskqB")},registered:function(){console.log("Service worker has been registered.")},cached:function(){console.log("Content has been cached for offline use.")},updatefound:function(){console.log("New content is downloading.")},updated:function(){console.log("New content is available; please refresh.")},offline:function(){console.log("No internet connection found. App is running in offline mode.")},error:function(s){console.error("Error during service worker registration:",s)}});var d=a("8c4f"),v=function(){var s=this,t=s.$createElement,a=s._self._c||t;return a("div",[a("header-banner"),a("div",{staticClass:"container"},[a("video-instruction"),a("result-month"),a("lessons-block"),a("app-download-block")],1),a("footer-landing")],1)},p=[],C=function(){var s=this,t=s.$createElement;s._self._c;return s._m(0)},u=[function(){var s=this,t=s.$createElement,i=s._self._c||t;return i("div",[i("div",{staticClass:"container__banner"},[i("div",{staticClass:"header"},[i("div",{staticClass:"logo"})]),i("div",{staticClass:"mobile__container"},[i("div",{staticClass:"mobile__content"},[i("h1",{staticClass:"title"},[s._v("Получай ежедневные сигналы рынка акций")]),i("div",{staticClass:"mobile__image"},[i("img",{attrs:{src:a("394f")}})]),i("a",{staticClass:"app__store__ico",attrs:{href:"#"}},[i("img",{attrs:{src:a("d334")}})])]),i("div",{staticClass:"mobile__image"},[i("img",{attrs:{src:a("394f")}})])])])])}],b={name:"header-banner"},m=b,f=(a("8739"),Object(c["a"])(m,C,u,!1,null,"0372d25e",null)),g=f.exports,y=function(){var s=this,t=s.$createElement;s._self._c;return s._m(0)},h=[function(){var s=this,t=s.$createElement,a=s._self._c||t;return a("div",{staticClass:"video__block__container"},[a("h2",{staticClass:"title"},[s._v("Как работает сервис")]),a("div",{staticClass:"video__block"},[a("iframe",{attrs:{width:"100%",height:"100%",src:"https://www.youtube-nocookie.com/embed/Jiz5MJneVmY",title:"YouTube video player",frameborder:"0",allow:"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture",allowfullscreen:""}})])])}],k={name:"videoInstruction"},S=k,w=(a("6aaa"),Object(c["a"])(S,y,h,!1,null,"2e50f6b1",null)),x=w.exports,M=function(){var s=this,t=s.$createElement,a=s._self._c||t;return a("div",[a("h2",{staticClass:"title"},[s._v("Наши результаты за месяц")]),a("VueSlickCarousel",s._b({staticClass:"slider__desktop"},"VueSlickCarousel",s.setting,!1),[a("div",[a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])])]),a("div",[a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])])]),a("div",[a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])])]),a("div",[a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])])]),a("div",[a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])])])]),a("VueSlickCarousel",s._b({staticClass:"slider__mobile"},"VueSlickCarousel",s.setting2,!1),[a("div",[a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])]),a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])])]),a("div",[a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])]),a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])])]),a("div",[a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])]),a("div",{staticClass:"slide__item"},[a("div",{staticClass:"top"},[a("div",{staticClass:"text__block"},[a("h3",{staticClass:"title__slide"},[s._v("Synopsys, Inc "),a("span",{staticClass:"type"},[s._v("SNPS")])]),a("div",[a("span",{staticClass:"course"},[s._v("14.42%")]),a("span",{staticClass:"time"},[s._v("за 25 дней")])])]),a("div",{staticClass:"logo__block"},[a("img",{attrs:{src:"https://media-exp1.licdn.com/dms/image/C4E0BAQEq7MVORIs3MA/company-logo_200_200/0/1519855929274?e=1626307200&v=beta&t=9d-g-XGSUpBhRLCXhECsZdSRLXlMrJYd9ofsjHMQoWw"}})])]),a("div",{staticClass:"bottom"},[a("div",{staticClass:"buy__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена покупки")]),a("span",{staticClass:"price__buy"},[s._v("$261.85")])]),a("div",{staticClass:"sell__block"},[a("span",{staticClass:"title__buy"},[s._v("Цена продажи")]),a("span",{staticClass:"price__buy"},[s._v("$280.00")])])])])])])],1)},E=[],$=a("a7ab"),R=a.n($),X=(a("7b8d"),a("6a2c"),{name:"resultMonth",components:{VueSlickCarousel:R.a},data:function(){return{setting:{arrows:!1,dots:!0,infinite:!0,slidesToShow:2,speed:100},setting2:{arrows:!1,dots:!0,infinite:!0,slidesToShow:1,speed:100}}}}),O=X,j=(a("6e16"),Object(c["a"])(O,M,E,!1,null,"d93546f6",null)),B=j.exports,A=function(){var s=this,t=s.$createElement;s._self._c;return s._m(0)},L=[function(){var s=this,t=s.$createElement,i=s._self._c||t;return i("div",{staticClass:"lesson__container"},[i("h2",{staticClass:"title"},[s._v("Получи 10 уроков "),i("br"),s._v("по инвестициям")]),i("div",{staticClass:"container__lessons"},[i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 1")]),i("p",{staticClass:"desc"},[s._v("Как и зачем начинать инвестировать?")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 2")]),i("p",{staticClass:"desc"},[s._v("Как правильно использовать сигналы и извлекать из этого максимальную прибыль?")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 3")]),i("p",{staticClass:"desc"},[s._v("У какого брокера открыть счёт?")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 4")]),i("p",{staticClass:"desc"},[s._v("9 ресурсов по отбору акций в свой портфель")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 5")]),i("p",{staticClass:"desc"},[s._v("Как из 8000 акций отобрать 10 перспективных в свой портфель?")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 6")]),i("p",{staticClass:"desc"},[s._v("Как я делаю 60% годовых на фондовом рынке?")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 7")]),i("p",{staticClass:"desc"},[s._v("Построение своей стратегии инвестирования")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 8")]),i("p",{staticClass:"desc"},[s._v("6 бесплатных источников по развитию навыков инвестирования")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 9")]),i("p",{staticClass:"desc"},[s._v("10 способов потерять деньги на фондовом рынке")])])]),i("a",{staticClass:"lessons__item",attrs:{href:"#"}},[i("div",{staticClass:"preview"},[i("img",{attrs:{src:a("86f1")}})]),i("div",{staticClass:"text"},[i("p",{staticClass:"title__less"},[s._v("Урок 10")]),i("p",{staticClass:"desc"},[s._v("Как я управляю 1 миллионном, который мне дал популярный блогер Михаил Литвин?")])])])])])}],I={name:"LessonsBlock"},Q=I,P=(a("12dc"),Object(c["a"])(Q,A,L,!1,null,"5eaf24ce",null)),V=P.exports,J=function(){var s=this,t=s.$createElement;s._self._c;return s._m(0)},H=[function(){var s=this,t=s.$createElement,i=s._self._c||t;return i("div",{staticClass:"container__apps"},[i("h2",{staticClass:"title"},[s._v("Скачивай"),i("br"),s._v(" прямо сейчас")]),i("a",{staticClass:"app__store__ico",attrs:{href:"#"}},[i("img",{attrs:{src:a("d334")}})])])}],N={name:"app-download-block"},Y=N,q=(a("2ceb"),Object(c["a"])(Y,J,H,!1,null,"6f0be2e4",null)),G=q.exports,U=function(){var s=this,t=s.$createElement;s._self._c;return s._m(0)},W=[function(){var s=this,t=s.$createElement,i=s._self._c||t;return i("div",{staticClass:"footer"},[i("div",{staticClass:"container"},[i("div",{staticClass:"links__block"},[i("a",{staticClass:"link",attrs:{href:"#"}},[s._v("Политика конфиденциальности")]),i("a",{staticClass:"link",attrs:{href:"#"}},[s._v("Пользовательское соглашение")])]),i("div",{staticClass:"soc__ico"},[i("a",{staticClass:"insta soc__btn",attrs:{href:"#"}},[i("img",{attrs:{src:a("53c3")}})]),i("a",{staticClass:"telegram soc__btn",attrs:{href:"#"}},[i("img",{attrs:{src:a("7315")}})])]),i("p",{staticClass:"copyright"},[s._v("Copyright 2021 Все права защищены")])])])}],Z={name:"footerLanding"},T=Z,F=(a("d8c3"),Object(c["a"])(T,U,W,!1,null,"52b7c388",null)),z=F.exports,D={name:"Home",components:{FooterLanding:z,AppDownloadBlock:G,LessonsBlock:V,ResultMonth:B,VideoInstruction:x,HeaderBanner:g}},K=D,ss=Object(c["a"])(K,v,p,!1,null,null,null),ts=ss.exports;i["a"].use(d["a"]);var as=[{path:"/",name:"Home",component:ts}],is=new d["a"]({routes:as}),es=is,ls=a("2f62");i["a"].use(ls["a"]);var cs=new ls["a"].Store({state:{},mutations:{},actions:{},modules:{}});a("a0d8");i["a"].config.productionTip=!1,new i["a"]({router:es,store:cs,render:function(s){return s(n)}}).$mount("#app")},"6aaa":function(s,t,a){"use strict";a("4119")},"6e16":function(s,t,a){"use strict";a("041d")},7247:function(s,t,a){},7315:function(s,t,a){s.exports=a.p+"img/telegram.3e03a6b5.svg"},"85ec":function(s,t,a){},"86f1":function(s,t,a){s.exports=a.p+"img/less1.04a6b4a8.png"},8739:function(s,t,a){"use strict";a("14b0")},"8ef7":function(s,t,a){},a0d8:function(s,t,a){},d334:function(s,t,a){s.exports=a.p+"img/app-store-ico.b7a52e7a.svg"},d8c3:function(s,t,a){"use strict";a("459e")}});
//# sourceMappingURL=app.bff79e62.js.map