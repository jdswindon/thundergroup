(function () {
	'use strict';

	/*!
	 * @copyright Copyright (c) 2017 IcoMoon.io
	 * @license   Licensed under MIT license
	 *            See https://github.com/Keyamoon/svgxuse
	 * @version   1.2.6
	 */
	(function(){if("undefined"!==typeof window&&window.addEventListener){var e=Object.create(null),l,d=function(){clearTimeout(l);l=setTimeout(n,100);},m=function(){},t=function(){window.addEventListener("resize",d,false);window.addEventListener("orientationchange",d,false);if(window.MutationObserver){var k=new MutationObserver(d);k.observe(document.documentElement,{childList:true,subtree:true,attributes:true});m=function(){try{k.disconnect(),window.removeEventListener("resize",d,!1),window.removeEventListener("orientationchange",
	d,!1);}catch(v){}};}else document.documentElement.addEventListener("DOMSubtreeModified",d,false),m=function(){document.documentElement.removeEventListener("DOMSubtreeModified",d,false);window.removeEventListener("resize",d,false);window.removeEventListener("orientationchange",d,false);};},u=function(k){function e(a){if(void 0!==a.protocol)var c=a;else c=document.createElement("a"),c.href=a;return c.protocol.replace(/:/g,"")+c.host}if(window.XMLHttpRequest){var d=new XMLHttpRequest;var m=e(location);k=e(k);d=void 0===
	d.withCredentials&&""!==k&&k!==m?XDomainRequest||void 0:XMLHttpRequest;}return d};var n=function(){function d(){--q;0===q&&(m(),t());}function l(a){return function(){ true!==e[a.base]&&(a.useEl.setAttributeNS("http://www.w3.org/1999/xlink","xlink:href","#"+a.hash),a.useEl.hasAttribute("href")&&a.useEl.setAttribute("href","#"+a.hash));}}function p(a){return function(){var c=document.body,b=document.createElement("x");a.onload=null;b.innerHTML=a.responseText;if(b=b.getElementsByTagName("svg")[0])b.setAttribute("aria-hidden",
	"true"),b.style.position="absolute",b.style.width=0,b.style.height=0,b.style.overflow="hidden",c.insertBefore(b,c.firstChild);d();}}function n(a){return function(){a.onerror=null;a.ontimeout=null;d();}}var a,c,q=0;m();var f=document.getElementsByTagName("use");for(c=0;c<f.length;c+=1){try{var g=f[c].getBoundingClientRect();}catch(w){g=false;}var h=(a=f[c].getAttribute("href")||f[c].getAttributeNS("http://www.w3.org/1999/xlink","href")||f[c].getAttribute("xlink:href"))&&a.split?a.split("#"):["",""];var b=
	h[0];h=h[1];var r=g&&0===g.left&&0===g.right&&0===g.top&&0===g.bottom;g&&0===g.width&&0===g.height&&!r?(f[c].hasAttribute("href")&&f[c].setAttributeNS("http://www.w3.org/1999/xlink","xlink:href",a),b.length&&(a=e[b],true!==a&&setTimeout(l({useEl:f[c],base:b,hash:h}),0),void 0===a&&(h=u(b),void 0!==h&&(a=new h,e[b]=a,a.onload=p(a),a.onerror=n(a),a.ontimeout=n(a),a.open("GET",b),a.send(),q+=1)))):r?b.length&&e[b]&&setTimeout(l({useEl:f[c],base:b,hash:h}),0):void 0===e[b]?e[b]=true:e[b].onload&&(e[b].abort(),
	delete e[b].onload,e[b]=true);}f="";q+=1;d();};var p=function(){window.removeEventListener("load",p,false);l=setTimeout(n,0);};"complete"!==document.readyState?window.addEventListener("load",p,false):p();}})();

	// binds $ to jquery, requires you to write strict code. Will fail validation if it doesn't match requirements.
	(function($) {

		// add all of your code within here, not above or below
		$(function() {
			

		});

	}(jQuery));

	// --------------------------------------------------------------------------------------------------
	// Mobile Menu - Opt 2
	// --------------------------------------------------------------------------------------------------

	// Set some vars for the icons
	var iconAngleUp =
	  "<svg class='icon icon-angle-up'><use xlink:href='" +
	  themeURL.themeURL +
	  "/_assets/images/icons-sprite.svg#icon-angle-up'></use></svg>";
	var iconAngleDown =
	  "<svg class='icon icon-angle-down'><use xlink:href='" +
	  themeURL.themeURL +
	  "/_assets/images/icons-sprite.svg#icon-angle-down'></use></svg>";

	// // Copy primary and secondary menus to .mob-nav element
	document.querySelector(".mob-nav .scroll-container");

	// Add dropdown arrow to links with sub-menus
	var subNavPosition = document.querySelectorAll(
	  ".mob-nav .menu-item-has-children > a"
	);
	// console.log(subNavPosition);
	subNavPosition.forEach((element) => {
	  var subArrows = document.createElement("span");
	  subArrows.setAttribute("class", "sub-arrow");
	  subArrows.innerHTML = iconAngleDown + iconAngleUp;
	  element.appendChild(subArrows);
	});

	// Get all the sub nav arrow icons
	var allArrows = document.querySelectorAll(".sub-arrow .icon-angle-down");

	// Add active class to all sub nav arrow icons
	allArrows.forEach((element) => {
	  element.classList.add("active");
	});

	// Get all sub navs
	var allSubArrows = document.querySelectorAll(".sub-arrow");
	document.querySelectorAll(".sub-menu");
	// For each sub nav, let it toggle a class and show/hide the sibling menu
	allSubArrows.forEach((subArrow) => {
	  if(typeof (subArrow.parentNode.nextElementSibling) != 'undefined' && subArrow.parentNode.nextElementSibling != null) {
	    subArrow.parentNode.nextElementSibling.style.display = "none";
	  }
	  subArrow.addEventListener("click", function (event) {
	    event.preventDefault();
	    // if(this.classList.contains("active") == false) {
	    //   allSubMenus.forEach(function(subMenu) {
	    //     subMenu.style.display = "none";
	    //   });
	    //   var allActive = document.querySelectorAll(".main-nav-item.active, .sub-arrow.active, .icon-angle-up.active");
	    //   allActive.forEach(function(activeItem) {
	    //     activeItem.classList.remove("active");
	    //     if(activeItem.classList.contains("icon-angle-up")) {
	    //       activeItem.previousElementSibling.classList.toggle("active");
	    //     }
	    //   });
	    // }
	    this.classList.toggle("active");
	    this.parentNode.classList.toggle("active");
	    this.parentNode.nextElementSibling.style.display =
	      this.parentNode.nextElementSibling.style.display === "none" ? "block" : "none";
	    var subArrowChildren = [...this.children];
	    subArrowChildren.forEach((child) => {
	      child.classList.toggle("active");
	    });
	  });
	});

	// Show underlay and fix the body scroll when menu button is clicked
	var menuBtns = document.querySelectorAll('[data-mobile-menu-toggle]');

	menuBtns.forEach(function(menuBtn) {
	  menuBtn.addEventListener("click", function () {
	    document.querySelector(".mob-nav").classList.toggle("mob-nav--active");
	    document.querySelectorAll("[data-mobile-menu-toggle] .open").forEach(function(open) {
	      open.classList.toggle("hide");
	    });
	    document.querySelectorAll("[data-mobile-menu-toggle] .close").forEach(function(close) {
	      close.classList.toggle("hide");
	    });
	  });
	});

	// Hide menu when close icon or underlay is clicked
	document.querySelector(".mob-nav-underlay");


	// Tabs
	var tabBtns = document.querySelectorAll('.menu-tab-btn');

	tabBtns.forEach(function(tabBtn) {
	  tabBtn.addEventListener('click',function() {
	    document.querySelector('.menu-tab-btn.active').classList.remove('active');
	    this.classList.add('active');

	    document.querySelector('.menu-tab.active').classList.remove('active');
	    var tab = document.querySelector('.menu-tab-' + this.dataset.menutab);
	    tab.classList.add('active');
	  });
	});

	// Open current page tab
	var currentPage = document.querySelector('.mob-nav .current-menu-item');
	if(currentPage != null) {
	  var currentPageParent = currentPage.parentElement;

	  if(currentPageParent.classList == "sub-menu") {
	    currentPageParent.style.display = "block";
	    var currentPageTab = currentPageParent.parentElement.parentElement;
	  } else {
	    var currentPageTab = currentPageParent;
	  }

	  var currentPageTabBtn = document.querySelector('[data-menutab="' + currentPageTab.dataset.menutabbtn + '"]');

	  // Open tab
	  document.querySelector('.menu-tab-btn.active').classList.remove('active');
	  currentPageTabBtn.classList.add('active');

	  document.querySelector('.menu-tab.active').classList.remove('active');
	  var tab = document.querySelector('.menu-tab-' + currentPageTabBtn.dataset.menutab);
	  tab.classList.add('active');
	}




	// --------------------------------------------------------------------------------------------------
	// Mobile Menu - Opt 1
	// --------------------------------------------------------------------------------------------------

	// // Set some vars for the icons
	// var iconAngleUp = "<svg class='icon icon-angle-up'><use xlink:href='" + themeURL.themeURL + "/_assets/images/icons-sprite.svg#icon-angle-up'></use></svg>";
	// var iconAngleDown =
	//     "<svg class='icon icon-angle-down'><use xlink:href='" + themeURL.themeURL + "/_assets/images/icons-sprite.svg#icon-angle-down'></use></svg>";

	// // // Copy primary and secondary menus to .mob-nav element
	// var mobNav = document.querySelector('.mob-nav .scroll-container');

	// // // Add Close Icon element
	// var closeBtn = document.createElement('div');
	// closeBtn.setAttribute('class', 'mob-nav-close');
	// closeBtn.innerHTML = "<svg class='icon icon-times'><use xlink:href='" + themeURL.themeURL + "/_assets/images/icons-sprite.svg#icon-times'></use></svg>";
	// mobNav.insertAdjacentElement('beforeend', closeBtn);

	// // Add dropdown arrow to links with sub-menus
	// var subNavPosition = document.querySelectorAll('.mob-nav .menu-item-has-children > a');
	// subNavPosition.forEach((element) => {
	//     var subArrows = document.createElement('span');
	//     subArrows.setAttribute('class', 'sub-arrow');
	//     subArrows.innerHTML = iconAngleDown + iconAngleUp;
	//     element.insertAdjacentElement('afterend', subArrows);
	// });

	// // Get all the sub nav arrow icons
	// var allArrows = document.querySelectorAll('.sub-arrow .icon-angle-down');

	// // Add active class to all sub nav arrow icons
	// allArrows.forEach((element) => {
	//     element.classList.add('active');
	// });

	// // Get all sub navs
	// var allSubArrows = document.querySelectorAll('.sub-arrow');

	// // For each sub nav, let it toggle a class and show/hide the sibling menu
	// allSubArrows.forEach((subArrow) => {
	//     if(typeof (subArrow.nextElementSibling) != 'undefined' && subArrow.nextElementSibling != null) {
	//         subArrow.nextElementSibling.style.display = 'none';
	//     }
	//     subArrow.addEventListener('click', function () {
	//         this.classList.toggle('active');
	//         this.previousElementSibling.classList.toggle('active');
	//         this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';
	//         var subArrowChildren = [...this.children];
	//         subArrowChildren.forEach((child) => {
	//             child.classList.toggle('active');
	//         });
	//     });
	// });

	// // Add underlay element after mobile nav
	// var mobNavUnderlay = document.createElement('div');
	// mobNavUnderlay.setAttribute('class', 'mob-nav-underlay');
	// document.querySelector('.mob-nav').insertAdjacentElement('afterend', mobNavUnderlay);

	// // Show underlay and fix the body scroll when menu button is clicked
	// var menuBtns = document.querySelectorAll('[data-mobile-menu-toggle]');

	// menuBtns.forEach(function(menuBtn) {
	//     menuBtn.addEventListener('click', function () {
	//         document.querySelector('.mob-nav-underlay').classList.toggle('mob-nav--active');
	//         document.querySelector('.mob-nav').classList.toggle('mob-nav--active');
	//         document.querySelector('body').classList.add('overflow-y-hidden')
	//         document.querySelector('body').classList.add('h-screen')
	//     });
	// });

	// // Hide menu when close icon or underlay is clicked
	// var closeMenuBtn = document.querySelector('.mob-nav-close');
	// var menuOverlay = document.querySelector('.mob-nav-underlay');

	// closeMenuBtn.addEventListener('click', closeMobileNav);
	// menuOverlay.addEventListener('click', closeMobileNav);

	// function closeMobileNav() {
	//     document.querySelector('.mob-nav-underlay').classList.remove('mob-nav--active');
	//     document.querySelector('.mob-nav').classList.remove('mob-nav--active');
	//     // document.querySelector('body').classList.remove('fixed')
	// }

	var lazysizes_min = {exports: {}};

	/*! lazysizes - v4.1.5 */

	var hasRequiredLazysizes_min;

	function requireLazysizes_min () {
		if (hasRequiredLazysizes_min) return lazysizes_min.exports;
		hasRequiredLazysizes_min = 1;
		(function (module) {
			!function(a,b){var c=b(a,a.document);a.lazySizes=c,module.exports&&(module.exports=c);}(window,function(a,b){if(b.getElementsByClassName){var c,d,e=b.documentElement,f=a.Date,g=a.HTMLPictureElement,h="addEventListener",i="getAttribute",j=a[h],k=a.setTimeout,l=a.requestAnimationFrame||k,m=a.requestIdleCallback,n=/^picture$/i,o=["load","error","lazyincluded","_lazyloaded"],p={},q=Array.prototype.forEach,r=function(a,b){return p[b]||(p[b]=new RegExp("(\\s|^)"+b+"(\\s|$)")),p[b].test(a[i]("class")||"")&&p[b]},s=function(a,b){r(a,b)||a.setAttribute("class",(a[i]("class")||"").trim()+" "+b);},t=function(a,b){var c;(c=r(a,b))&&a.setAttribute("class",(a[i]("class")||"").replace(c," "));},u=function(a,b,c){var d=c?h:"removeEventListener";c&&u(a,b),o.forEach(function(c){a[d](c,b);});},v=function(a,d,e,f,g){var h=b.createEvent("Event");return e||(e={}),e.instance=c,h.initEvent(d,!f,!g),h.detail=e,a.dispatchEvent(h),h},w=function(b,c){var e;!g&&(e=a.picturefill||d.pf)?(c&&c.src&&!b[i]("srcset")&&b.setAttribute("srcset",c.src),e({reevaluate:true,elements:[b]})):c&&c.src&&(b.src=c.src);},x=function(a,b){return (getComputedStyle(a,null)||{})[b]},y=function(a,b,c){for(c=c||a.offsetWidth;c<d.minSize&&b&&!a._lazysizesWidth;)c=b.offsetWidth,b=b.parentNode;return c},z=function(){var a,c,d=[],e=[],f=d,g=function(){var b=f;for(f=d.length?e:d,a=true,c=false;b.length;)b.shift()();a=false;},h=function(d,e){a&&!e?d.apply(this,arguments):(f.push(d),c||(c=true,(b.hidden?k:l)(g)));};return h._lsFlush=g,h}(),A=function(a,b){return b?function(){z(a);}:function(){var b=this,c=arguments;z(function(){a.apply(b,c);});}},B=function(a){var b,c=0,e=d.throttleDelay,g=d.ricTimeout,h=function(){b=false,c=f.now(),a();},i=m&&g>49?function(){m(h,{timeout:g}),g!==d.ricTimeout&&(g=d.ricTimeout);}:A(function(){k(h);},true);return function(a){var d;(a=true===a)&&(g=33),b||(b=true,d=e-(f.now()-c),d<0&&(d=0),a||d<9?i():k(i,d));}},C=function(a){var b,c,d=99,e=function(){b=null,a();},g=function(){var a=f.now()-c;a<d?k(g,d-a):(m||e)(e);};return function(){c=f.now(),b||(b=k(g,d));}};!function(){var b,c={lazyClass:"lazyload",loadedClass:"lazyloaded",loadingClass:"lazyloading",preloadClass:"lazypreload",errorClass:"lazyerror",autosizesClass:"lazyautosizes",srcAttr:"data-src",srcsetAttr:"data-srcset",sizesAttr:"data-sizes",minSize:40,customMedia:{},init:true,expFactor:1.5,hFac:.8,loadMode:2,loadHidden:true,ricTimeout:0,throttleDelay:125};d=a.lazySizesConfig||a.lazysizesConfig||{};for(b in c)b in d||(d[b]=c[b]);a.lazySizesConfig=d,k(function(){d.init&&F();});}();var D=function(){var g,l,m,o,p,y,D,F,G,H,I,J,K,L,M=/^img$/i,N=/^iframe$/i,O="onscroll"in a&&!/(gle|ing)bot/.test(navigator.userAgent),P=0,Q=0,R=0,S=-1,T=function(a){R--,a&&a.target&&u(a.target,T),(!a||R<0||!a.target)&&(R=0);},U=function(a,c){var d,f=a,g="hidden"==x(b.body,"visibility")||"hidden"!=x(a.parentNode,"visibility")&&"hidden"!=x(a,"visibility");for(F-=c,I+=c,G-=c,H+=c;g&&(f=f.offsetParent)&&f!=b.body&&f!=e;)(g=(x(f,"opacity")||1)>0)&&"visible"!=x(f,"overflow")&&(d=f.getBoundingClientRect(),g=H>d.left&&G<d.right&&I>d.top-1&&F<d.bottom+1);return g},V=function(){var a,f,h,j,k,m,n,p,q,r=c.elements;if((o=d.loadMode)&&R<8&&(a=r.length)){f=0,S++,null==K&&("expand"in d||(d.expand=e.clientHeight>500&&e.clientWidth>500?500:370),J=d.expand,K=J*d.expFactor),Q<K&&R<1&&S>2&&o>2&&!b.hidden?(Q=K,S=0):Q=o>1&&S>1&&R<6?J:P;for(;f<a;f++)if(r[f]&&!r[f]._lazyRace)if(O)if((p=r[f][i]("data-expand"))&&(m=1*p)||(m=Q),q!==m&&(y=innerWidth+m*L,D=innerHeight+m,n=-1*m,q=m),h=r[f].getBoundingClientRect(),(I=h.bottom)>=n&&(F=h.top)<=D&&(H=h.right)>=n*L&&(G=h.left)<=y&&(I||H||G||F)&&(d.loadHidden||"hidden"!=x(r[f],"visibility"))&&(l&&R<3&&!p&&(o<3||S<4)||U(r[f],m))){if(ba(r[f]),k=true,R>9)break}else !k&&l&&!j&&R<4&&S<4&&o>2&&(g[0]||d.preloadAfterLoad)&&(g[0]||!p&&(I||H||G||F||"auto"!=r[f][i](d.sizesAttr)))&&(j=g[0]||r[f]);else ba(r[f]);j&&!k&&ba(j);}},W=B(V),X=function(a){s(a.target,d.loadedClass),t(a.target,d.loadingClass),u(a.target,Z),v(a.target,"lazyloaded");},Y=A(X),Z=function(a){Y({target:a.target});},$=function(a,b){try{a.contentWindow.location.replace(b);}catch(c){a.src=b;}},_=function(a){var b,c=a[i](d.srcsetAttr);(b=d.customMedia[a[i]("data-media")||a[i]("media")])&&a.setAttribute("media",b),c&&a.setAttribute("srcset",c);},aa=A(function(a,b,c,e,f){var g,h,j,l,o,p;(o=v(a,"lazybeforeunveil",b)).defaultPrevented||(e&&(c?s(a,d.autosizesClass):a.setAttribute("sizes",e)),h=a[i](d.srcsetAttr),g=a[i](d.srcAttr),f&&(j=a.parentNode,l=j&&n.test(j.nodeName||"")),p=b.firesLoad||"src"in a&&(h||g||l),o={target:a},p&&(u(a,T,true),clearTimeout(m),m=k(T,2500),s(a,d.loadingClass),u(a,Z,true)),l&&q.call(j.getElementsByTagName("source"),_),h?a.setAttribute("srcset",h):g&&!l&&(N.test(a.nodeName)?$(a,g):a.src=g),f&&(h||l)&&w(a,{src:g})),a._lazyRace&&delete a._lazyRace,t(a,d.lazyClass),z(function(){(!p||a.complete&&a.naturalWidth>1)&&(p?T(o):R--,X(o));},true);}),ba=function(a){var b,c=M.test(a.nodeName),e=c&&(a[i](d.sizesAttr)||a[i]("sizes")),f="auto"==e;(!f&&l||!c||!a[i]("src")&&!a.srcset||a.complete||r(a,d.errorClass)||!r(a,d.lazyClass))&&(b=v(a,"lazyunveilread").detail,f&&E.updateElem(a,true,a.offsetWidth),a._lazyRace=true,R++,aa(a,b,f,e,c));},ca=function(){if(!l){if(f.now()-p<999)return void k(ca,999);var a=C(function(){d.loadMode=3,W();});l=true,d.loadMode=3,W(),j("scroll",function(){3==d.loadMode&&(d.loadMode=2),a();},true);}};return {_:function(){p=f.now(),c.elements=b.getElementsByClassName(d.lazyClass),g=b.getElementsByClassName(d.lazyClass+" "+d.preloadClass),L=d.hFac,j("scroll",W,true),j("resize",W,true),a.MutationObserver?new MutationObserver(W).observe(e,{childList:true,subtree:true,attributes:true}):(e[h]("DOMNodeInserted",W,true),e[h]("DOMAttrModified",W,true),setInterval(W,999)),j("hashchange",W,true),["focus","mouseover","click","load","transitionend","animationend","webkitAnimationEnd"].forEach(function(a){b[h](a,W,true);}),/d$|^c/.test(b.readyState)?ca():(j("load",ca),b[h]("DOMContentLoaded",W),k(ca,2e4)),c.elements.length?(V(),z._lsFlush()):W();},checkElems:W,unveil:ba}}(),E=function(){var a,c=A(function(a,b,c,d){var e,f,g;if(a._lazysizesWidth=d,d+="px",a.setAttribute("sizes",d),n.test(b.nodeName||""))for(e=b.getElementsByTagName("source"),f=0,g=e.length;f<g;f++)e[f].setAttribute("sizes",d);c.detail.dataAttr||w(a,c.detail);}),e=function(a,b,d){var e,f=a.parentNode;f&&(d=y(a,f,d),e=v(a,"lazybeforesizes",{width:d,dataAttr:!!b}),e.defaultPrevented||(d=e.detail.width)&&d!==a._lazysizesWidth&&c(a,f,e,d));},f=function(){var b,c=a.length;if(c)for(b=0;b<c;b++)e(a[b]);},g=C(f);return {_:function(){a=b.getElementsByClassName(d.autosizesClass),j("resize",g);},checkElems:g,updateElem:e}}(),F=function(){F.i||(F.i=true,E._(),D._());};return c={cfg:d,autoSizer:E,loader:D,init:F,uP:w,aC:s,rC:t,hC:r,fire:v,gW:y,rAF:z}}}); 
		} (lazysizes_min));
		return lazysizes_min.exports;
	}

	requireLazysizes_min();

	var fslightbox = {exports: {}};

	var hasRequiredFslightbox;

	function requireFslightbox () {
		if (hasRequiredFslightbox) return fslightbox.exports;
		hasRequiredFslightbox = 1;
		(function (module, exports) {
			!function(e,t){module.exports=t();}(window,(function(){return function(e){var t={};function n(o){if(t[o])return t[o].exports;var i=t[o]={i:o,l:false,exports:{}};return e[o].call(i.exports,i,i.exports,n),i.l=true,i.exports}return n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:true,get:o});},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:true});},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:true,value:e}),2&t&&"string"!=typeof e)for(var i in e)n.d(o,i,function(t){return e[t]}.bind(null,i));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=0)}([function(e,t,n){n.r(t);var o,i="fslightbox-",s="".concat(i,"styles"),r="".concat(i,"cursor-grabbing"),a="".concat(i,"full-dimension"),c="".concat(i,"flex-centered"),l="".concat(i,"open"),u="".concat(i,"transform-transition"),d="".concat(i,"absoluted"),f="".concat(i,"slide-btn"),p="".concat(f,"-container"),h="".concat(i,"fade-in"),g="".concat(i,"fade-out"),m=h+"-strong",b=g+"-strong",v="".concat(i,"opacity-"),x="".concat(v,"1"),y="".concat(i,"source");function w(e){return (w="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function S(e){var t=e.stageIndexes,n=e.core.stageManager,o=e.props.sources.length-1;n.getPreviousSlideIndex=function(){return 0===t.current?o:t.current-1},n.getNextSlideIndex=function(){return t.current===o?0:t.current+1},n.updateStageIndexes=0===o?function(){}:1===o?function(){0===t.current?(t.next=1,delete t.previous):(t.previous=0,delete t.next);}:function(){t.previous=n.getPreviousSlideIndex(),t.next=n.getNextSlideIndex();},n.i=o<=2?function(){return  true}:function(e){var n=t.current;if(0===n&&e===o||n===o&&0===e)return  true;var i=n-e;return  -1===i||0===i||1===i};}"object"===("undefined"==typeof document?"undefined":w(document))&&((o=document.createElement("style")).className=s,o.appendChild(document.createTextNode(".fslightbox-absoluted{position:absolute;top:0;left:0}.fslightbox-fade-in{animation:fslightbox-fade-in .3s cubic-bezier(0,0,.7,1)}.fslightbox-fade-out{animation:fslightbox-fade-out .3s ease}.fslightbox-fade-in-strong{animation:fslightbox-fade-in-strong .3s cubic-bezier(0,0,.7,1)}.fslightbox-fade-out-strong{animation:fslightbox-fade-out-strong .3s ease}@keyframes fslightbox-fade-in{from{opacity:.65}to{opacity:1}}@keyframes fslightbox-fade-out{from{opacity:.35}to{opacity:0}}@keyframes fslightbox-fade-in-strong{from{opacity:.3}to{opacity:1}}@keyframes fslightbox-fade-out-strong{from{opacity:1}to{opacity:0}}.fslightbox-cursor-grabbing{cursor:grabbing}.fslightbox-full-dimension{width:100%;height:100%}.fslightbox-open{overflow:hidden;height:100%}.fslightbox-flex-centered{display:flex;justify-content:center;align-items:center}.fslightbox-opacity-0{opacity:0!important}.fslightbox-opacity-1{opacity:1!important}.fslightbox-scrollbarfix{padding-right:17px}.fslightbox-transform-transition{transition:transform .3s}.fslightbox-container{font-family:Arial,sans-serif;position:fixed;top:0;left:0;background:linear-gradient(rgba(30,30,30,.9),#000 1810%);touch-action:pinch-zoom;z-index:1000000000;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-tap-highlight-color:transparent}.fslightbox-container *{box-sizing:border-box}.fslightbox-svg{width:20px;height:20px}.fslightbox-svgp{transition:fill .15s ease;fill:#ddd}.fslightbox-nav{height:45px;width:100%;position:absolute;top:0;left:0}.fslightboxsn{z-index:0;display:flex;align-items:center;margin:14px 0 0 11px;font-size:15px;color:#d7d7d7}.fslightboxsn span{display:inline;vertical-align:middle}.fslightboxsl{display:inline-block!important;margin:0 5px;width:1px;height:12px;transform:rotate(15deg);background:white}.fslightbox-toolbar{position:absolute;z-index:3;right:0;top:0;height:100%;display:flex}.fslightbox-toolbar-button{width:45px;height:100%}.fslightbox-fsx{width:24px;height:24px}.fslightboxb{border:0;background:rgba(35,35,35,.65);cursor:pointer}.fslightboxb:focus{outline:0}.fslightboxb:focus .fslightbox-svgp{fill:#fff}.fslightboxb:hover .fslightbox-svgp{fill:#fff}.fslightbox-slide-btn-container{display:flex;align-items:center;padding:12px 12px 12px 6px;position:absolute;top:50%;cursor:pointer;z-index:3;transform:translateY(-50%)}.fslightbox-slide-btn-container-next{right:0;padding-left:12px;padding-right:3px}@media (min-width:476px){.fslightbox-slide-btn-container{padding:22px 22px 22px 6px}.fslightbox-slide-btn-container-next{padding-right:6px!important;padding-left:22px}}@media (min-width:768px){.fslightbox-slide-btn-container{padding:30px 30px 30px 6px}.fslightbox-slide-btn-container-next{padding-left:30px}.fslightbox-slide-btn{padding:10px}}.fslightbox-slide-btn-container:hover .fslightbox-svgp{fill:#fff}.fslightbox-slide-btn{padding:9px}.fslightbox-slide-btn-container-previous{left:0}@media (max-width:475.99px){.fslightbox-slide-btn-container-previous{padding-left:3px}}.fslightbox-down-event-detector{position:absolute;z-index:1}.fslightbox-slide-swiping-hoverer{z-index:4}.fslightbox-invalid-file-wrapper{font-size:22px;color:#eaebeb;margin:auto}.fslightboxv{object-fit:cover}.fslightbox-youtube-iframe{border:0}.fslightboxl{display:block;margin:auto;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:67px;height:67px}.fslightboxl div{box-sizing:border-box;display:block;position:absolute;width:54px;height:54px;margin:6px;border:5px solid;border-color:#999 transparent transparent transparent;border-radius:50%;animation:fslightboxl 1.2s cubic-bezier(.5,0,.5,1) infinite}.fslightboxl div:nth-child(1){animation-delay:-.45s}.fslightboxl div:nth-child(2){animation-delay:-.3s}.fslightboxl div:nth-child(3){animation-delay:-.15s}@keyframes fslightboxl{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}.fslightbox-source{position:relative;z-index:2;opacity:0}@media (min-width:1200px){.fslightboxsn{margin:15px 0 0 12px;font-size:16px;display:block}.fslightboxsl{margin:0 6px 1px 6px;height:14px}.fslightbox-slide-btn{padding:11px}.fslightbox-svg{width:22px;height:22px}.fslightbox-fsx{width:26px;height:26px}.fslightbox-fso{width:22px;height:22px}.fslightboxl div{width:60px;height:60px;border-width:6px;border-color:#999 transparent transparent transparent;border-radius:50%}}@media (min-width:1600px){.fslightbox-nav{height:50px}.fslightboxsn{display:flex;margin:19px 0 0 16px;font-size:20px}.fslightboxsl{margin:0 7px 1px 7px;height:16px;width:2px;background:#d7d7d7}.fslightbox-toolbar-button{width:50px}.fslightbox-slide-btn{padding:12px}.fslightbox-svg{width:24px;height:24px}.fslightbox-fsx{width:28px;height:28px}.fslightbox-fso{width:24px;height:24px}}")),document.head.appendChild(o));function L(e){var t,n=e.props,o=0,i={};this.getSourceTypeFromLocalStorageByUrl=function(e){return t[e]?t[e]:s(e)},this.handleReceivedSourceTypeForUrl=function(e,n){if(false===i[n]&&(o--,"invalid"!==e?i[n]=e:delete i[n],0===o)){!function(e,t){for(var n in t)e[n]=t[n];}(t,i);try{localStorage.setItem("fslightbox-types",JSON.stringify(t));}catch(e){}}};var s=function(e){o++,i[e]=false;};if(n.disableLocalStorage)this.getSourceTypeFromLocalStorageByUrl=function(){},this.handleReceivedSourceTypeForUrl=function(){};else {try{t=JSON.parse(localStorage.getItem("fslightbox-types"));}catch(e){}t||(t={},this.getSourceTypeFromLocalStorageByUrl=s);}}function C(e,t,n,o){e.data;var i=e.elements.sources,s=n/o,r=0;this.adjustSize=function(){if((r=e.mw/s)<e.mh)return n<e.mw&&(r=o),a();r=o>e.mh?e.mh:o,a();};var a=function(){i[t].style.width=r*s+"px",i[t].style.height=r+"px";};}function A(e,t){var n=this,o=e.collections.sourceSizers,i=e.elements,s=i.sourceAnimationWrappers,r=i.sources,a=e.isl,c=e.props.onSourceLoad,l=e.resolve;function u(e,n){o[t]=l(C,[t,e,n]),o[t].adjustSize();}this.b=function(e,o){r[t].classList.add(x),n.a(),u(e,o),n.b=u;},this.a=function(){a[t]=true,s[t].classList.add(m),s[t].removeChild(s[t].firstChild),c&&c(e,r[t],t);};}function E(e,t){var n,o=this,i=e.elements.sources,s=e.props,r=(0, e.resolve)(A,[t]);this.handleImageLoad=function(e){var t=e.target,n=t.naturalWidth,o=t.naturalHeight;r.b(n,o);},this.handleVideoLoad=function(e){var t=e.target,o=t.videoWidth,i=t.videoHeight;n=true,r.b(o,i);},this.handleNotMetaDatedVideoLoad=function(){n||o.handleYoutubeLoad();},this.handleYoutubeLoad=function(e,t){e||(e=1920,t=1080),s.maxYoutubeDimensions&&(e=s.maxYoutubeDimensions.width,t=s.maxYoutubeDimensions.height),r.b(e,t);},this.handleCustomLoad=function(){var e=i[t],n=e.offsetWidth,s=e.offsetHeight;n&&s?r.b(n,s):setTimeout(o.handleCustomLoad);};}function F(e,t,n){var o=e.elements.sources,i=e.props.customClasses,s=i[t]?i[t]:"";o[t].className=n+" "+s;}function I(e,t){var n=e.elements.sources,o=e.props.customAttributes;for(var i in o[t])n[t].setAttribute(i,o[t][i]);}function z(e,t){var n=e.collections.sourceLoadHandlers,o=e.elements,i=o.sources,s=o.sourceAnimationWrappers,r=e.props.sources;i[t]=document.createElement("img"),F(e,t,y),i[t].src=r[t],i[t].onload=n[t].handleImageLoad,I(e,t),s[t].appendChild(i[t]);}function T(e,t){var n=e.ap,o=e.collections.sourceLoadHandlers,i=e.elements,s=i.sources,r=i.sourceAnimationWrappers,a=e.props,c=a.sources,l=a.videosPosters,u=document.createElement("video"),d=document.createElement("source");s[t]=u,F(e,t,"".concat(y," fslightboxv")),u.src=c[t],u.onloadedmetadata=function(e){return o[t].handleVideoLoad(e)},u.controls=true,u.autoplay=n.i(t),I(e,t),l[t]&&(s[t].poster=l[t]),d.src=c[t],u.appendChild(d),setTimeout(o[t].handleNotMetaDatedVideoLoad,3e3),r[t].appendChild(s[t]);}function N(e,t){var n=e.ap,o=e.collections.sourceLoadHandlers,s=e.elements,r=s.sources,a=s.sourceAnimationWrappers,c=e.props.sources[t],l=c.split("?")[1],u=document.createElement("iframe");r[t]=u,F(e,t,"".concat(y," ").concat(i,"youtube-iframe")),u.src="https://www.youtube.com/embed/".concat(c.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/)[2],"?").concat(l||"").concat(n.i(t)?"&mute=1&autoplay=1":"","&enablejsapi=1"),u.allowFullscreen=true,I(e,t),a[t].appendChild(u),o[t].handleYoutubeLoad(parseInt(u.width),parseInt(u.height));}function P(e,t){var n=e.collections.sourceLoadHandlers,o=e.elements,i=o.sources,s=o.sourceAnimationWrappers,r=e.props.sources;i[t]=r[t],F(e,t,"".concat(i[t].className," ").concat(y)),s[t].appendChild(i[t]),n[t].handleCustomLoad();}function k(e,t){var n=e.elements,o=n.sources,s=n.sourceAnimationWrappers;e.props.sources;o[t]=document.createElement("div"),o[t].className="".concat(i,"invalid-file-wrapper ").concat(c),o[t].innerHTML="Invalid source",s[t].appendChild(o[t]),new A(e,t).a();}function R(e){var t=e.collections,n=t.sourceLoadHandlers,o=t.sourcesRenderFunctions,i=e.core.sourceDisplayFacade,s=e.resolve;this.runActionsForSourceTypeAndIndex=function(t,r){var a;switch("invalid"!==t&&(n[r]=s(E,[r])),t){case "image":a=z;break;case "video":a=T;break;case "youtube":a=N;break;case "custom":a=P;break;default:a=k;}o[r]=function(){return a(e,r)},i.displaySourcesWhichShouldBeDisplayed();};}function M(e,t,n){var o=e.props,i=o.types,s=o.type,r=o.sources;this.getTypeSetByClientForIndex=function(e){var t;return i&&i[e]?t=i[e]:s&&(t=s),t},this.retrieveTypeWithXhrForIndex=function(e){!function(e,t){var n=document.createElement("a");n.href=e;var o=n.hostname;if("www.youtube.com"===o||"youtu.be"===o)return t("youtube");var i=new XMLHttpRequest;i.onreadystatechange=function(){if(4!==i.readyState){if(2===i.readyState){var e,n=i.getResponseHeader("content-type");switch(n.slice(0,n.indexOf("/"))){case "image":e="image";break;case "video":e="video";break;default:e="invalid";}i.onreadystatechange=null,i.abort(),t(e);}}else t("invalid");},i.open("GET",e),i.send();}(r[e],(function(o){t.handleReceivedSourceTypeForUrl(o,r[e]),n.runActionsForSourceTypeAndIndex(o,e);}));};}function H(e,t){var n=e.core.stageManager,o=e.elements,i=o.smw,s=o.sourceWrappersContainer,r=e.props,l=0,f=document.createElement("div");function p(e){f.style.transform="translateX(".concat(e+l,"px)"),l=0;}function h(){return (1+r.slideDistance)*innerWidth}f.className="".concat(d," ").concat(a," ").concat(c),f.s=function(){f.style.display="flex";},f.h=function(){f.style.display="none";},f.a=function(){f.classList.add(u);},f.d=function(){f.classList.remove(u);},f.n=function(){f.style.removeProperty("transform");},f.v=function(e){return l=e,f},f.ne=function(){p(-h());},f.z=function(){p(0);},f.p=function(){p(h());},n.i(t)||f.h(),i[t]=f,s.appendChild(f),function(e,t){var n=e.elements,o=n.smw,i=n.sourceAnimationWrappers,s=document.createElement("div"),r=document.createElement("div");r.className="fslightboxl";for(var a=0;a<3;a++){var c=document.createElement("div");r.appendChild(c);}s.appendChild(r),o[t].appendChild(s),i[t]=s;}(e,t);}function W(e,t,n){var o=document.createElementNS("http://www.w3.org/2000/svg","svg"),s="".concat(i,"svg");o.setAttributeNS(null,"class","".concat(s)),o.setAttributeNS(null,"viewBox",t);var r=document.createElementNS("http://www.w3.org/2000/svg","path");return r.setAttributeNS(null,"class","".concat(s,"p")),r.setAttributeNS(null,"d",n),o.appendChild(r),e.appendChild(o),o}function D(e,t){var n=document.createElement("button");return n.className="fslightboxb ".concat(i,"toolbar-button ").concat(c),n.title=t,e.appendChild(n),n}function O(e,t){var n=document.createElement("div");n.className="".concat(i,"toolbar"),t.appendChild(n),function(e,t){if(!e.hfs){var n="M4.5 11H3v4h4v-1.5H4.5V11zM3 7h1.5V4.5H7V3H3v4zm10.5 6.5H11V15h4v-4h-1.5v2.5zM11 3v1.5h2.5V7H15V3h-4z",o=D(t);o.title="Enter fullscreen";var s=W(o,"0 0 18 18",n);e.fso=function(){e.ifs=1,o.title="Exit fullscreen",s.classList.add("".concat(i,"fsx")),s.setAttributeNS(null,"viewBox","0 0 950 1024"),s.firstChild.setAttributeNS(null,"d","M682 342h128v84h-212v-212h84v128zM598 810v-212h212v84h-128v128h-84zM342 342v-128h84v212h-212v-84h128zM214 682v-84h212v212h-84v-128h-128z");},e.fsx=function(){e.ifs=0,o.title="Enter fullscreen",s.classList.remove("".concat(i,"fsx")),s.setAttributeNS(null,"viewBox","0 0 18 18"),s.firstChild.setAttributeNS(null,"d",n);},o.onclick=e.fs.t;}}(e,n),function(e,t){var n=D(t,"Close");n.onclick=e.core.lightboxCloser.closeLightbox,W(n,"0 0 24 24","M 4.7070312 3.2929688 L 3.2929688 4.7070312 L 10.585938 12 L 3.2929688 19.292969 L 4.7070312 20.707031 L 12 13.414062 L 19.292969 20.707031 L 20.707031 19.292969 L 13.414062 12 L 20.707031 4.7070312 L 19.292969 3.2929688 L 12 10.585938 L 4.7070312 3.2929688 z");}(e,n);}function j(e){var t=e.props.sources,n=e.elements.container,o=document.createElement("div");o.className="".concat(i,"nav"),n.appendChild(o),O(e,o),t.length>1&&function(e,t){var n=e.props.sources,o=(e.stageIndexes,document.createElement("div")),i=document.createElement("span"),s=document.createElement("span"),r=document.createElement("span");o.className="fslightboxsn",e.sn=function(e){return i.innerHTML=e},s.className="fslightboxsl",r.innerHTML=n.length,o.appendChild(i),o.appendChild(s),o.appendChild(r),t.appendChild(o);}(e,o);}function X(e,t,n,o){var i=e.elements.container,s=n.charAt(0).toUpperCase()+n.slice(1),r=document.createElement("div");r.className="".concat(p," ").concat(p,"-").concat(n),r.title="".concat(s," slide"),r.onclick=t,function(e,t){var n=document.createElement("button");n.className="fslightboxb ".concat(f," ").concat(c),W(n,"0 0 20 20",t),e.appendChild(n);}(r,o),i.appendChild(r);}function q(e){var t=e.core,n=t.lightboxCloser,o=t.slideChangeFacade,i=e.fs;this.listener=function(e){switch(e.key){case "Escape":n.closeLightbox();break;case "ArrowLeft":o.changeToPrevious();break;case "ArrowRight":o.changeToNext();break;case "F11":e.preventDefault(),i.t();}};}function B(e){var t=e.elements,n=e.sourcePointerProps,o=e.stageIndexes;function i(e,o){t.smw[e].v(n.swipedX)[o]();}this.runActionsForEvent=function(e){var s,a,c;t.container.contains(t.slideSwipingHoverer)||t.container.appendChild(t.slideSwipingHoverer),s=t.container,a=r,(c=s.classList).contains(a)||c.add(a),n.swipedX=e.screenX-n.downScreenX;var l=o.previous,u=o.next;i(o.current,"z"),void 0!==l&&n.swipedX>0?i(l,"ne"):void 0!==u&&n.swipedX<0&&i(u,"p");};}function V(e){var t=e.dss,n=e.props.sources,o=e.resolve,i=e.sourcePointerProps,s=o(B);1===n.length||t?this.listener=function(){i.swipedX=1;}:this.listener=function(e){i.isPointering&&s.runActionsForEvent(e);};}function U(e){var t=e.core.slideIndexChanger,n=e.elements.smw,o=e.stageIndexes,i=e.sws;function s(e){var t=n[o.current];t.a(),t[e]();}function r(e,t){ void 0!==e&&(n[e].s(),n[e][t]());}this.runPositiveSwipedXActions=function(){var e=o.previous;if(void 0===e)s("z");else {s("p");var n=o.next;t.changeTo(e);var a=o.previous;i.d(a),i.b(n),s("z"),r(a,"ne");}},this.runNegativeSwipedXActions=function(){var e=o.next;if(void 0===e)s("z");else {s("ne");var n=o.previous;t.changeTo(e);var a=o.next;i.d(a),i.b(n),s("z"),r(a,"p");}};}function _(e,t){e.contains(t)&&e.removeChild(t);}function Y(e){var t=e.core.lightboxCloser,n=e.dss,o=e.elements,i=e.props,s=e.resolve,a=e.sourcePointerProps,c=s(U);this.runNoSwipeActions=function(){_(o.container,o.slideSwipingHoverer),a.isSourceDownEventTarget||i.disableBackgroundClose||t.closeLightbox(),a.isPointering=false;},this.runActions=function(){n||(a.swipedX>0?c.runPositiveSwipedXActions():c.runNegativeSwipedXActions()),_(o.container,o.slideSwipingHoverer),o.container.classList.remove(r),a.isPointering=false;};}function J(e){var t=e.resolve,n=e.sourcePointerProps,o=t(Y);this.listener=function(){n.isPointering&&(n.swipedX?o.runActions():o.runNoSwipeActions());};}function G(e){var t=this,n=e.core,o=n.globalEventsController,i=n.scrollbarRecompensor,s=(e.data,e.e),r=e.elements,a=e.fs,c=e.props,u=e.sourcePointerProps;this.runActions=function(){t.i=1,r.container.classList.add(b),o.removeListeners(),c.exitFullscreenOnClose&&e.ifs&&a.x(),setTimeout((function(){t.i=0,u.isPointering=false,r.container.classList.remove(b),document.documentElement.classList.remove(l),i.removeRecompense(),document.body.removeChild(r.container),s("onClose");}),270);};}function $(e,t){var n=e.classList;n.contains(t)&&n.remove(t);}function K(e){var t,n,o,i,s,r,a,c,l;!function(e){var t=e.ap,n=e.elements.sources,o=e.props,i=o.autoplay,s=o.autoplays;function r(e,o){if("play"!=o||t.i(e)){var i=n[e];if(i){var s=i.tagName;if("VIDEO"==s)i[o]();else if("IFRAME"==s){var r=i.contentWindow;r&&r.postMessage('{"event":"command","func":"'.concat(o,'Video","args":""}'),"*");}}}}t.i=function(e){return s[e]||i&&0!=s[e]},t.p=function(e){r(e,"play");},t.c=function(e,t){r(e,"pause"),r(t,"play");};}(e),function(e){e.data;var t=e.fs,n=["fullscreenchange","webkitfullscreenchange","mozfullscreenchange","MSFullscreenChange"],o=document.documentElement,i=o.requestFullscreen;function s(e){for(var t=0;t<n.length;t++)document[e](n[t],r);}function r(){document.fullscreenElement||document.webkitIsFullScreen||document.mozFullScreen||document.msFullscreenElement?e.fso():e.fsx();}t.i=function(){if(i||(i=o.mozRequestFullScreen),i||(i=o.webkitRequestFullscreen),i||(i=o.msRequestFullscreen),!i)return e.hfs=1,t.o=function(){},t.x=function(){},t.t=function(){},t.l=function(){},void(t.q=function(){});t.o=function(){e.fso();var t=document.documentElement;t.requestFullscreen?t.requestFullscreen():t.mozRequestFullScreen?t.mozRequestFullScreen():t.webkitRequestFullscreen?t.webkitRequestFullscreen():t.msRequestFullscreen&&t.msRequestFullscreen();},t.x=function(){e.fsx(),document.exitFullscreen?document.exitFullscreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitExitFullscreen?document.webkitExitFullscreen():document.msExitFullscreen&&document.msExitFullscreen();},t.t=function(){e.ifs?t.x():t.o();},t.l=function(){s("addEventListener");},t.q=function(){s("removeEventListener");};};}(e),n=(t=e).core,o=n.globalEventsController,i=n.windowResizeActioner,s=t.fs,r=t.resolve,a=r(q),c=r(V),l=r(J),o.attachListeners=function(){document.addEventListener("pointermove",c.listener),document.addEventListener("pointerup",l.listener),addEventListener("resize",i.runActions),document.addEventListener("keydown",a.listener),s.l();},o.removeListeners=function(){document.removeEventListener("pointermove",c.listener),document.removeEventListener("pointerup",l.listener),removeEventListener("resize",i.runActions),document.removeEventListener("keydown",a.listener),s.q();},function(e){var t=e.core.lightboxCloser,n=(0, e.resolve)(G);t.closeLightbox=function(){n.isLightboxFadingOut||n.runActions();};}(e),function(e){var t=e.data,n=e.core.scrollbarRecompensor;function o(){document.body.offsetHeight>innerHeight&&(document.body.style.marginRight=t.scrollbarWidth+"px");}n.addRecompense=function(){"complete"===document.readyState?o():addEventListener("load",(function(){o(),n.addRecompense=o;}));},n.removeRecompense=function(){document.body.style.removeProperty("margin-right");};}(e),function(e){var t=e.core,n=t.slideChangeFacade,o=t.slideIndexChanger,i=t.stageManager;e.props.sources.length>1?(n.changeToPrevious=function(){o.jumpTo(i.getPreviousSlideIndex());},n.changeToNext=function(){o.jumpTo(i.getNextSlideIndex());}):(n.changeToPrevious=function(){},n.changeToNext=function(){});}(e),function(e){var t=e.ap,n=(e.componentsServices,e.core),o=n.slideIndexChanger,i=n.sourceDisplayFacade,s=n.stageManager,r=e.elements,a=r.smw,c=r.sourceAnimationWrappers,l=e.isl,u=e.stageIndexes,d=e.sws;o.changeTo=function(n){t.c(u.current,n),u.current=n,s.updateStageIndexes(),e.sn(n+1),i.displaySourcesWhichShouldBeDisplayed();},o.jumpTo=function(e){var t=u.previous,n=u.current,i=u.next,r=l[n],f=l[e];o.changeTo(e);for(var p=0;p<a.length;p++)a[p].d();d.d(n),d.c(),requestAnimationFrame((function(){requestAnimationFrame((function(){var e=u.previous,o=u.next;function p(){s.i(n)?n===u.previous?a[n].ne():n===u.next&&a[n].p():(a[n].h(),a[n].n());}r&&c[n].classList.add(g),f&&c[u.current].classList.add(h),d.a(),void 0!==e&&e!==n&&a[e].ne(),a[u.current].n(),void 0!==o&&o!==n&&a[o].p(),d.b(t),d.b(i),l[n]?setTimeout(p,260):p();}));}));};}(e),function(e){var t=e.core.sourcesPointerDown,n=e.elements,o=n.smw,i=n.sources,s=e.sourcePointerProps,r=e.stageIndexes;t.listener=function(e){"VIDEO"!==e.target.tagName&&e.preventDefault(),s.isPointering=true,s.downScreenX=e.screenX,s.swipedX=0;var t=i[r.current];t&&t.contains(e.target)?s.isSourceDownEventTarget=true:s.isSourceDownEventTarget=false;for(var n=0;n<o.length;n++)o[n].d();};}(e),function(e){var t=e.collections.sourcesRenderFunctions,n=e.core.sourceDisplayFacade,o=e.loc,i=e.stageIndexes;function s(e){t[e]&&(t[e](),delete t[e]);}n.displaySourcesWhichShouldBeDisplayed=function(){if(o)s(i.current);else for(var e in i)s(i[e]);};}(e),function(e){var t=e.core.stageManager,n=e.elements,o=n.smw,i=n.sourceAnimationWrappers,s=e.isl,r=e.stageIndexes,a=e.sws;a.a=function(){for(var e in r)o[r[e]].s();},a.b=function(e){ void 0===e||t.i(e)||(o[e].h(),o[e].n());},a.c=function(){for(var e in r)a.d(r[e]);},a.d=function(e){if(s[e]){var t=i[e];$(t,m),$(t,h),$(t,g);}};}(e),function(e){var t=e.collections.sourceSizers,n=e.core.windowResizeActioner,o=(e.data,e.elements.smw),i=e.props.sourceMargin,s=e.stageIndexes,r=1-2*i;n.runActions=function(){innerWidth>992?e.mw=r*innerWidth:e.mw=innerWidth,e.mh=r*innerHeight;for(var n=0;n<o.length;n++)o[n].d(),t[n]&&t[n].adjustSize();var i=s.previous,a=s.next;void 0!==i&&o[i].ne(),void 0!==a&&o[a].p();};}(e);}function Q(e){var t=e.ap,n=(e.componentsServices,e.core),o=n.globalEventsController,s=n.scrollbarRecompensor,r=n.sourceDisplayFacade,c=n.stageManager,u=n.windowResizeActioner,f=e.data,p=e.e,h=e.elements,g=(e.props,e.stageIndexes),b=e.sws,v=0;function x(){var t,n,o=e.props,s=o.autoplay,r=o.autoplays;v=true,function(e){var t=e.props,n=t.autoplays;e.c=t.sources.length;for(var o=0;o<e.c;o++)"false"===n[o]&&(n[o]=0),""===n[o]&&(n[o]=1);e.dss=t.disableSlideSwiping,e.loc=t.loadOnlyCurrentSource;}(e),f.scrollbarWidth=function(){var e=document.createElement("div"),t=e.style,n=document.createElement("div");t.visibility="hidden",t.width="100px",t.msOverflowStyle="scrollbar",t.overflow="scroll",n.style.width="100%",document.body.appendChild(e);var o=e.offsetWidth;e.appendChild(n);var i=n.offsetWidth;return document.body.removeChild(e),o-i}(),(s||r.length>0)&&(e.loc=1),K(e),e.fs.i(),h.container=document.createElement("div"),h.container.className="".concat(i,"container ").concat(a," ").concat(m),h.container.setAttribute("tabindex","0"),function(e){var t=e.elements;t.slideSwipingHoverer=document.createElement("div"),t.slideSwipingHoverer.className="".concat(i,"slide-swiping-hoverer ").concat(a," ").concat(d);}(e),j(e),function(e){var t=e.core.sourcesPointerDown,n=e.elements,o=e.props.sources,i=document.createElement("div");i.className="".concat(d," ").concat(a),n.container.appendChild(i),i.addEventListener("pointerdown",t.listener),n.sourceWrappersContainer=i;for(var s=0;s<o.length;s++)H(e,s);}(e),e.props.sources.length>1&&(n=(t=e).core.slideChangeFacade,X(t,n.changeToPrevious,"previous","M18.271,9.212H3.615l4.184-4.184c0.306-0.306,0.306-0.801,0-1.107c-0.306-0.306-0.801-0.306-1.107,0L1.21,9.403C1.194,9.417,1.174,9.421,1.158,9.437c-0.181,0.181-0.242,0.425-0.209,0.66c0.005,0.038,0.012,0.071,0.022,0.109c0.028,0.098,0.075,0.188,0.142,0.271c0.021,0.026,0.021,0.061,0.045,0.085c0.015,0.016,0.034,0.02,0.05,0.033l5.484,5.483c0.306,0.307,0.801,0.307,1.107,0c0.306-0.305,0.306-0.801,0-1.105l-4.184-4.185h14.656c0.436,0,0.788-0.353,0.788-0.788S18.707,9.212,18.271,9.212z"),X(t,n.changeToNext,"next","M1.729,9.212h14.656l-4.184-4.184c-0.307-0.306-0.307-0.801,0-1.107c0.305-0.306,0.801-0.306,1.106,0l5.481,5.482c0.018,0.014,0.037,0.019,0.053,0.034c0.181,0.181,0.242,0.425,0.209,0.66c-0.004,0.038-0.012,0.071-0.021,0.109c-0.028,0.098-0.075,0.188-0.143,0.271c-0.021,0.026-0.021,0.061-0.045,0.085c-0.015,0.016-0.034,0.02-0.051,0.033l-5.483,5.483c-0.306,0.307-0.802,0.307-1.106,0c-0.307-0.305-0.307-0.801,0-1.105l4.184-4.185H1.729c-0.436,0-0.788-0.353-0.788-0.788S1.293,9.212,1.729,9.212z")),function(e){for(var t=e.props.sources,n=e.resolve,o=n(L),i=n(R),s=n(M,[o,i]),r=0;r<t.length;r++)if("string"==typeof t[r]){var a=s.getTypeSetByClientForIndex(r);if(a)i.runActionsForSourceTypeAndIndex(a,r);else {var c=o.getSourceTypeFromLocalStorageByUrl(t[r]);c?i.runActionsForSourceTypeAndIndex(c,r):s.retrieveTypeWithXhrForIndex(r);}}else i.runActionsForSourceTypeAndIndex("custom",r);}(e),p("onInit");}e.open=function(){var n=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,i=g.previous,a=g.current,d=g.next;g.current=n,v||S(e),c.updateStageIndexes(),v?(b.c(),b.a(),b.b(i),b.b(a),b.b(d),p("onShow")):x(),r.displaySourcesWhichShouldBeDisplayed(),e.sn(n+1),document.body.appendChild(h.container),h.container.focus(),document.documentElement.classList.add(l),s.addRecompense(),o.attachListeners(),u.runActions(),h.smw[n].n(),t.p(n),p("onOpen");};}function Z(e,t,n){return (Z=ee()?Reflect.construct.bind():function(e,t,n){var o=[null];o.push.apply(o,t);var i=new(Function.bind.apply(e,o));return n&&te(i,n.prototype),i}).apply(null,arguments)}function ee(){if("undefined"==typeof Reflect||!Reflect.construct)return  false;if(Reflect.construct.sham)return  false;if("function"==typeof Proxy)return  true;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return  false}}function te(e,t){return (te=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e})(e,t)}function ne(e){return function(e){if(Array.isArray(e))return oe(e)}(e)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||function(e,t){if(!e)return;if("string"==typeof e)return oe(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);"Object"===n&&e.constructor&&(n=e.constructor.name);if("Map"===n||"Set"===n)return Array.from(e);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return oe(e,t)}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function oe(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,o=new Array(t);n<t;n++)o[n]=e[n];return o}function ie(){for(var e=document.getElementsByTagName("a"),t=function(t){if(!e[t].hasAttribute("data-fslightbox"))return "continue";var n=e[t].hasAttribute("data-href")?e[t].getAttribute("data-href"):e[t].getAttribute("href");if(!n)return console.warn('The "data-fslightbox" attribute was set without the "href" attribute.'),"continue";var o=e[t].getAttribute("data-fslightbox");fsLightboxInstances[o]||(fsLightboxInstances[o]=new FsLightbox);var i=null;"#"===n.charAt(0)?(i=document.getElementById(n.substring(1)).cloneNode(true)).removeAttribute("id"):i=n,fsLightboxInstances[o].props.sources.push(i),fsLightboxInstances[o].elements.a.push(e[t]);var s=fsLightboxInstances[o].props.sources.length-1;e[t].onclick=function(e){e.preventDefault(),fsLightboxInstances[o].open(s);},d("types","data-type"),d("videosPosters","data-video-poster"),d("customClasses","data-class"),d("customClasses","data-custom-class"),d("autoplays","data-autoplay");for(var r=["href","data-fslightbox","data-href","data-type","data-video-poster","data-class","data-custom-class","data-autoplay"],a=e[t].attributes,c=fsLightboxInstances[o].props.customAttributes,l=0;l<a.length;l++)if(-1===r.indexOf(a[l].name)&&"data-"===a[l].name.substr(0,5)){c[s]||(c[s]={});var u=a[l].name.substr(5);c[s][u]=a[l].value;}function d(n,i){e[t].hasAttribute(i)&&(fsLightboxInstances[o].props[n][s]=e[t].getAttribute(i));}},n=0;n<e.length;n++)t(n);var o=Object.keys(fsLightboxInstances);window.fsLightbox=fsLightboxInstances[o[o.length-1]];}window.FsLightbox=function(){var e=this;this.props={sources:[],customAttributes:[],customClasses:[],autoplays:[],types:[],videosPosters:[],exitFullscreenOnClose:1,sourceMargin:.05,slideDistance:.3},this.data={isFullscreenOpen:false,scrollbarWidth:0},this.isl=[],this.sourcePointerProps={downScreenX:null,isPointering:false,isSourceDownEventTarget:false,swipedX:0},this.stageIndexes={},this.elements={a:[],container:null,slideSwipingHoverer:null,smw:[],sourceWrappersContainer:null,sources:[],sourceAnimationWrappers:[]},this.sn=function(){},this.resolve=function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[];return n.unshift(e),Z(t,ne(n))},this.collections={sourceLoadHandlers:[],sourcesRenderFunctions:[],sourceSizers:[]},this.core={globalEventsController:{},lightboxCloser:{},lightboxUpdater:{},scrollbarRecompensor:{},slideChangeFacade:{},slideIndexChanger:{},sourcesPointerDown:{},sourceDisplayFacade:{},stageManager:{},windowResizeActioner:{}},this.ap={},this.fs={},this.sws={},this.e=function(t){e.props[t]&&e.props[t](e);},Q(this),this.close=function(){return e.core.lightboxCloser.closeLightbox()};},window.fsLightboxInstances={},ie(),window.refreshFsLightbox=function(){for(var e in fsLightboxInstances){var t=fsLightboxInstances[e].props;fsLightboxInstances[e]=new FsLightbox,fsLightboxInstances[e].props=t,fsLightboxInstances[e].props.sources=[],fsLightboxInstances[e].elements.a=[];}ie();};}])})); 
		} (fslightbox));
		return fslightbox.exports;
	}

	requireFslightbox();

	// --------------------------------------------------------------------------------------------------
	// Back to top
	// -- Uses IntersectionObserver API to determine if the window has
	// -- scrolled past the pixel-to-watch (in footer.twig)
	// -- Not supported in IE11 & < Safari 12.0 only
	// --------------------------------------------------------------------------------------------------

	if ("IntersectionObserver" in window) {
	// // Set a variable to identify the back to top button
	  var backToTopBtn = document.getElementById("back-top");

	  var observer = new IntersectionObserver((entries) => {
	    if (entries[0].boundingClientRect.y < 0) {
	      // Add the opacity class to bring in the
	      backToTopBtn.classList.add("opacity-25");
	      backToTopBtn.classList.remove("opacity-0");
	      // Bring it into the viewport (removing from the viewport means it isn't clickable)
	      backToTopBtn.classList.remove("-mb-20");
	    } else {
	      backToTopBtn.classList.remove("opacity-25");
	      backToTopBtn.classList.add("opacity-0");
	      // Move it back out of the viewport
	      backToTopBtn.classList.add("-mb-20");
	    }
	  });
	  observer.observe(document.querySelector("#pixel-to-watch"));
	  backToTopBtn.onclick = function () {
	    window.scroll({
	      top: 0,
	      left: 0,
	      behaviour: "smooth",
	    });
	  };
	}

	class AjaxCart {
	  constructor() {
	    this.slideout = document.querySelector('.cart-slideout');
	    this.cartOpenOpeners = document.querySelectorAll('[data-ajax-open-cart]');
	    this.cartOpenButtons = document.querySelectorAll('.ajax_add_to_cart');

	    if (this.cartOpenOpeners != null && this.slideout != null) {
	      this.initOpeners();
	    }

	    if (this.cartOpenButtons != null && this.slideout != null) {
	      this.initButtons();
	    }
	  }

	  initOpeners() {
	    Array.prototype.forEach.call(this.cartOpenButtons, (button) => {
	      button.addEventListener('click', (e) => {
	        e.preventDefault();
	        this.openMenu();
	      });
	    });
	  }

	  initButtons() {
	    Array.prototype.forEach.call(this.cartOpenButtons, (button) => {
	      button.addEventListener('click', (e) => {
	        e.preventDefault();
	        this.openMenu();
	      });
	    });
	  }

	  openMenu() {
	    this.slideout.classList.add('active');
	    this.closeHandler();
	  }

	  closeHandler() {
	    const closeBtn = this.slideout.querySelector('[data-ajax-cart-close]');

	    if (closeBtn != null) {
	      closeBtn.addEventListener('click', () => this.slideout.classList.remove('active'));
	    }
	  }

	  cartUpdate() {}
	}

	class ProductTabs {
	  constructor() {
	    this.tabLinks = document.querySelectorAll('.product-tab-link');
	    this.tabPanels = document.querySelectorAll('.product-tab-panel');

	    if (this.tabLinks != null && this.tabPanels != null) {
	      this.init();
	    }
	  }

	  init() {
	    Array.prototype.forEach.call(this.tabLinks, (link) => {
	      link.addEventListener('click', (e) => {
	        e.preventDefault();
	        this.linkClickHandler(link);
	      });
	    });
	  }

	  async linkClickHandler(link) {
	    const tab = document.querySelector(`${link.getAttribute('href')}`);

	    if (tab != null) {
	      this.closeTabs();

	      link.classList.add('active');
	      tab.classList.remove('hidden');
	    }
	  }

	  closeTabs() {
	    Array.prototype.forEach.call(this.tabLinks, (link) => {
	      link.classList.remove('active');
	    });

	    Array.prototype.forEach.call(this.tabPanels, (panel) => {
	      panel.classList.add('hidden');
	    });
	  }
	}

	class ReviewStars {
	  constructor() {
	    // We have to wait for the DOM to be loaded on this one as the stars are inserted with JS via WooCommerce.
	    document.addEventListener('DOMContentLoaded', (e) => {
	      this.starHolder = document.querySelector('.comment-form-rating .stars');
	      this.stars = document.querySelectorAll('.comment-form-rating .stars a');

	      if (this.starHolder != null && this.stars != null) {
	        this.init();
	      }
	    });
	  }

	  init() {
	    this.starHolder.addEventListener('mouseleave', (e) => {
	      Array.prototype.forEach.call(this.stars, (star, key) => {
	        star.style.color = '';
	      });
	    });

	    Array.prototype.forEach.call(this.stars, (star, key) => {
	      star.addEventListener('mouseover', (e) => {
	        e.preventDefault();
	        this.mouseoverHandler(key);
	      });

	      /* This is controlled via WooCommerce (adds active to star and sets the select box.)
	       If you delete something/remove jquery you may need to add this in here. */

	      star.addEventListener('click', async (e) => {
	        await this.clickHandler(key);
	      });
	    });
	  }

	  /**
	   * Loop through each star, and add or remove the `.selected` class
	   */
	  mouseoverHandler(selectedIndex) {
	    Array.prototype.forEach.call(this.stars, (star, key) => {
	      if (key <= selectedIndex) {
	        star.style.color = 'gold';
	      } else {
	        star.style.color = '';
	      }
	    });
	  }

	  clickHandler(selectedIndex) {
	    Array.prototype.forEach.call(this.stars, (star, key) => {
	      if (key <= selectedIndex) {
	        star.classList.add('selected');
	      } else {
	        star.classList.remove('selected');
	      }
	    });
	  }
	}

	new AjaxCart();
	new ProductTabs();
	new ReviewStars();

})();
