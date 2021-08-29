!function ($) {
    'use strict';

	$(document).foundation();

	/**
	 * DOMContentLoaded
	 */
	document.addEventListener( 'DOMContentLoaded', () => {

		/**attribute target="_blank" is not W3C compliant*/
		const _blanks = document.querySelectorAll('a._blank, a.blank, a[target="_blank"]');
		Array.prototype.forEach.call(_blanks, (el) => {
			el.removeAttribute('target');
			el.setAttribute('target', '_blank');
			if (!1 === el.hasAttribute('rel')) {
				el.setAttribute('rel', 'noopener noreferrer nofollow');
			}
		});
	});

    /**
     * https://webhd.vn/
     * @apx
     */
    $(function () {

		const currentDevice = device.noConflict();

		/**
		 * @return {*}
		 * @private
		 */
		function _is_mobile() {
			return currentDevice.mobile() || Foundation.MediaQuery.upTo('small');
		}

		/**********/
		const footerDropdownBtns = Array.from(document.querySelectorAll(".footer-widget .widget_nav_menu .widget-title"));
		const footerDropdownContent = Array.from(document.querySelectorAll(".footer-widget .widget_nav_menu"));
		footerDropdownBtns.forEach((item, i) => item.addEventListener("click", () => {
			$(footerDropdownContent[i].lastElementChild).find('ul.menu').slideToggle();
			$(footerDropdownContent[i].firstElementChild).toggleClass("open");
		}));

		/**********/
		const sidebar_dropdown_a = Array.from(document.querySelectorAll(".sidebar--wrap ul.menu .menu-item-has-children > a"));
		sidebar_dropdown_a.forEach((item, i) => item.addEventListener("click", (event) => {
			event.preventDefault();
			$(item).closest('li').find('ul.sub-menu').slideToggle();
			$(item).toggleClass("open");
		}));

		/*swiper*/
		const _swiper_container = document.querySelectorAll(".w-swiper");
		Array.prototype.forEach.call(_swiper_container, (el, index) => {
			const _rand = random_string(12),
				_class = 'swiper-' + _rand,
				_next_class = 'next-' + _rand,
				_prev_class = 'prev-' + _rand,
				_pagination_class = 'pagination-' + _rand,
			 	_scrollbar_class = 'scrollbar-' + _rand;

			el.classList.add(_class);
			var el_swiper_wrapper = el.querySelector('.swiper-wrapper');
			var _row_data = el_swiper_wrapper.dataset.row,
				_autoview_data = el_swiper_wrapper.dataset.autoview,
				_desktop_data = el_swiper_wrapper.dataset.desktop,
				_tablet_data = el_swiper_wrapper.dataset.tablet,
				_mobile_data = el_swiper_wrapper.dataset.mobile,
				_pagination_data = el_swiper_wrapper.dataset.pagination,
				_navigation_data = el_swiper_wrapper.dataset.navigation,
				_autoplay_data = el_swiper_wrapper.dataset.autoplay,
				_freeMode_data = el_swiper_wrapper.dataset.freemode,
				_fade_data = el_swiper_wrapper.dataset.fade,
				_loop_data = el_swiper_wrapper.dataset.loop,
				_gap_data = el_swiper_wrapper.dataset.gap,
				_vertical_data = el_swiper_wrapper.dataset.vertical,
				_autoHeight_data = el_swiper_wrapper.dataset.autoheight,
				_slidesPerGroup_data = el_swiper_wrapper.dataset.group,
				_delay_data = el_swiper_wrapper.dataset.delay,
				_speed_data = el_swiper_wrapper.dataset.speeds,
				_observer_data = el_swiper_wrapper.dataset.observer,
				_parallax_data = el_swiper_wrapper.dataset.parallax,
				_scrollbar_data = el_swiper_wrapper.dataset.scrollbar,
				_progress_data = el_swiper_wrapper.dataset.progressbar,
				_centered_data = el_swiper_wrapper.dataset.centered,
				_marquee_data = el_swiper_wrapper.dataset.marquee,
				_reverse_data = el_swiper_wrapper.dataset.reverse;

			/* swiper controls*/
			var _controls = el.closest('.swiper-section').querySelector('.swiper-controls');
			if (_controls == null) {
				_controls = document.createElement("div");
				_controls.classList.add('swiper-controls');
				el.after(_controls);
			}

			/*get data value*/
			if (!_desktop_data) _desktop_data = 1;
			if (!_tablet_data) _tablet_data = 1;
			if (!_mobile_data) _mobile_data = 1;

			var _options = {};
			if(_gap_data) {
				_options.spaceBetween = 20;
			}

			if (_autoview_data) {
				_options.slidesPerView = 'auto';
				_options.loopedSlides = 12;
				if (_gap_data) {
					_options.breakpoints = {
						768: {spaceBetween: 30}
					}
				}
			} else {
				_options.slidesPerView = parseInt(_mobile_data);
				if (_gap_data) {
					_options.breakpoints = {
						768: {
							spaceBetween: 30,
							slidesPerView: parseInt(_tablet_data)
						},
						1024: {
							spaceBetween: 30,
							slidesPerView: parseInt(_desktop_data)
						},
					}
				} else {
					_options.breakpoints = {
						768: { slidesPerView: parseInt(_tablet_data) },
						1024: { slidesPerView: parseInt(_desktop_data) },
					}
				}
			}

			if ( _autoview_data || _options.slidesPerView > 1 ) {
				_options.watchSlidesVisibility = !0;
			}

			_options.grabCursor = !0;
			_options.allowTouchMove = !0;
			_options.threshold = 0.5;
			_options.watchSlidesProgress = !0;
			//_options.watchSlidesVisibility = !0;
			_options.keyboard = {enabled: !0};
			//_options.mousewheel = !1;
			_options.hashNavigation = {watchState: !0};

			if (_centered_data) {
				_options.centeredSlides = !0;
			}

			if (!_speed_data) {
				_speed_data = getMinMax(600, 1200);
			}
			_options.speed = parseInt(_speed_data);

			if (_observer_data) {
				_options.observer = !0;
				_options.observeParents = !0;
			}

			if(_row_data) {
				_options.slidesPerColumn = parseInt(_row_data);
				_options.slidesPerColumnFill = 'row';
				_options.direction = 'horizontal';
				_options.loop = !1;
			}
			if (_slidesPerGroup_data && !_autoview_data) {
				_options.slidesPerGroupSkip = !0;
				_options.loopFillGroupWithBlank = !0;
				_options.slidesPerGroup = parseInt(_slidesPerGroup_data);
			}
			if (_fade_data) {
				_options.effect = 'fade';
				_options.fadeEffect = {crossFade: !0};
			}
			if (_autoHeight_data) {
				_options.autoHeight = !0;
			}
			if (_freeMode_data) {
				_options.freeMode = !0;
			}
			if (_loop_data && !_row_data) {
				_options.loop = !0;
				_options.loopFillGroupWithBlank = !0;
			}
			if (_autoplay_data) {
				if (_delay_data) {
					_options.autoplay = {
						disableOnInteraction: !1,
						delay: parseInt(_delay_data),
					};
				} else {
					// default delay
					_options.autoplay = {
						disableOnInteraction: !1,
						delay: 6000,
					};
				}
				if (_reverse_data) {
					_options.reverseDirection = !0;
				}
			}

			/*navigation*/
			if (_navigation_data) {

				var _section = el.closest('.swiper-section');
				var _btn_prev = _section.querySelector('.swiper-button-prev');
				var _btn_next = _section.querySelector('.swiper-button-next');
				if (_btn_prev && _btn_next) {
					_btn_prev.classList.add(_prev_class);
					_btn_next.classList.add(_next_class);
				} else {

					_btn_prev = document.createElement("div");
					_btn_next = document.createElement("div");
					if (_parallax_data) {
						_btn_prev.classList.add('swiper-button', 'parallax-layer', 'swiper-button-prev', _prev_class);
						_btn_next.classList.add('swiper-button', 'parallax-layer', 'swiper-button-next', _next_class);
					} else {
						_btn_prev.classList.add('swiper-button', 'swiper-button-prev', _prev_class);
						_btn_next.classList.add('swiper-button', 'swiper-button-next', _next_class);
					}
					_controls.appendChild(_btn_prev);
					_controls.appendChild(_btn_next);
					_btn_prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 50 50" xml:space="preserve" style="">\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M18.416,24.5L32.584,24.5" class="draw_0"></path>\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M27.529,19.541L32.488,24.5L27.529,29.459" class="draw_1"></path>\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M18.416,24.5L32.584,24.5" class="draw_4"></path>\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M27.529,19.541L32.488,24.5L27.529,29.459" class="draw_5"></path>\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M4.844999999999999,24.363A20,20 0,1,1 44.845,24.363A20,20 0,1,1 4.844999999999999,24.363" class="draw_2"></path>\n' +
						'                        <path fill="none" stroke-miterlimit="10" d="M4.844999999999999,24.363A20,20 0,1,1 44.845,24.363A20,20 0,1,1 4.844999999999999,24.363" class="draw_3"></path>\n' +
						'                     </svg>';
					_btn_next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 50 50" xml:space="preserve" style="">\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M18.416,24.5L32.584,24.5" class="draw_0"></path>\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M27.529,19.541L32.488,24.5L27.529,29.459" class="draw_1"></path>\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M18.416,24.5L32.584,24.5" class="draw_4"></path>\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M27.529,19.541L32.488,24.5L27.529,29.459" class="draw_5"></path>\n' +
						'                        <path fill="none" stroke="#888888" stroke-miterlimit="10" d="M4.844999999999999,24.363A20,20 0,1,1 44.845,24.363A20,20 0,1,1 4.844999999999999,24.363" class="draw_2"></path>\n' +
						'                        <path fill="none" stroke-miterlimit="10" d="M4.844999999999999,24.363A20,20 0,1,1 44.845,24.363A20,20 0,1,1 4.844999999999999,24.363" class="draw_3"></path>\n' +
						'                     </svg>';
				}

				_options.navigation = {
					nextEl: '.' + _next_class,
					prevEl: '.' + _prev_class,
				};
			}

			if (_pagination_data) {
				let _swiper_pagination = document.createElement("div");
				if (_parallax_data) {
					_swiper_pagination.classList.add('swiper-pagination', 'parallax-layer', _pagination_class);
				} else {
					_swiper_pagination.classList.add('swiper-pagination', _pagination_class);
				}

				_controls.appendChild(_swiper_pagination);
				if (_pagination_data === 'fraction') {
					_options.pagination = {
						el: '.' + _pagination_class,
						type: 'fraction',
					};
				} else if (_pagination_data === 'progressbar') {
					_options.pagination = {
						el: '.' + _pagination_class,
						type: "progressbar",
					};
				} else if (_pagination_data === 'dynamic') {
					_options.pagination = {
						dynamicBullets: !0,
						el: '.' + _pagination_class,
					};
				} else {
					_options.pagination = {
						dynamicBullets: !1,
						el: '.' + _pagination_class,
					};
				}

				_options.pagination.clickable = !0;
			}

			if (_scrollbar_data) {
				let _swiper_scrollbar = document.createElement("div");
				if (_parallax_data) {
					_swiper_scrollbar.classList.add('swiper-scrollbar', 'parallax-layer', _scrollbar_class);
				} else {
					_swiper_scrollbar.classList.add('swiper-scrollbar', _scrollbar_class);
				}
				_controls.appendChild(_swiper_scrollbar);
				_options.scrollbar = {
					hide: !0,
					el: '.' + _scrollbar_class,
				}
			}

			if (_vertical_data) {
				_options.direction = 'vertical';
			}

			/**parallax*/
			if(_parallax_data) {
				_options.parallax = !0;
			}

			/**_marquee**/
			if (_marquee_data) {
				_options.centeredSlides = !0;
				_options.autoplay = {
					delay: 1,
					disableOnInteraction: !1
				};
				_options.loop = !0;
				_options.allowTouchMove = !0;
			}

			/*cssMode*/
			if (!_marquee_data && !_centered_data && !_freeMode_data && !_progress_data && _is_mobile() && !el.classList.contains('sync-swiper')) {
				_options.cssMode = !0; /*sử dụng API CSS Scroll Snap */
			}

			/*progress*/
			if (_progress_data) {
				let _swiper_progress = document.createElement("div");
				if (_parallax_data) {
					_swiper_progress.classList.add('swiper-progress', 'parallax-layer');
				} else {
					_swiper_progress.classList.add('swiper-progress');
				}

				_controls.appendChild(_swiper_progress);
			}

			var _swiper_progress = _controls.querySelector('.swiper-progress');

			/** init*/
			_options.on = {
				init: function () {
					let t = this;
					if (_parallax_data) {
						t.autoplay.stop();
						t.touchEventsData.formElements = "*";
						const parallax = el.querySelectorAll('.--bg');
						[].slice.call(parallax).map( (elem) => {
							let p = elem.dataset.swiperParallax.replace("%", "");
							if (!p) p = 95;
							elem.dataset.swiperParallax = p / 100 * t.width;
						});
					}

					if (_progress_data) {
						_swiper_progress.classList.add('progress');
					}
				},
				slideChange: function() {
					if (_progress_data) {
						_swiper_progress.classList.remove('progress');
					}

					// sync
					let t = this;
					if (el.classList.contains('sync-swiper')) {
						const el_closest = el.closest('section.section');
						const sync_swipers = Array.from(el_closest.querySelectorAll('.sync-swiper:not(.sync-exclude)'));
						sync_swipers.forEach((item, i) => {
							var _local_swiper = item.swiper;
							if (_loop_data) {
								_local_swiper.slideToLoop(t.activeIndex, parseInt(_speed_data), true);
							} else {
								_local_swiper.slideTo(t.activeIndex, parseInt(_speed_data), true);
							}
						});
					}
				},
				slideChangeTransitionEnd: function() {
					if (_progress_data) {
						_swiper_progress.classList.add('progress');
					}
				}
			}

			/*console.log(_options);*/
			var _swiper = new Swiper('.' + _class, _options);
			if (!_autoplay_data && !_marquee_data) {
				_swiper.autoplay.stop();
			}

			/* now add mouseover and mouseout events to pause and resume the autoplay;*/
			el.addEventListener('mouseover', () => {
				_swiper.autoplay.stop();
			});
			el.addEventListener('mouseout', () => {
				if (_autoplay_data) {
					_swiper.autoplay.start();
				}
			});
		});

		/*footer*/
		$('.footer-widget ul.menu a').attr('tabindex', '0');

		/*mega menu*/
		var mega_menu = $('.mega-menu');
		if (mega_menu.length) {
			mega_menu.closest('.dropdown.menu').css('position', 'unset');
		}

		/*new-title*/
		var _new_title = $('li.new-title, li.new');
		_new_title.each(function( index, el ) {
			var tmp = $(this).find('a').html();
			$(this).find('a').html(tmp + ' -&nbsp;<span class="new-label">NEW</span>');
		});

		/*toggle menu*/
		const _toggle_menu = $(".toggle_menu");
		_toggle_menu.find("li.is-active.has-submenu-toggle").find(".submenu-toggle").trigger('click');
    });

	//console.log(window.location);
}(jQuery);
