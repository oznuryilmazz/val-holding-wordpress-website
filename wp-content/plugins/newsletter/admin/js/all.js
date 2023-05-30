const TNPModal = function (options) {
    'use strict'

    const _options = {
        title: '',
        content: '',
        contentSelector: '',
        showClose: true,
        onClose: null,
        closeWhenClickOutside: true,
        confirmText: 'CONFIRM',
        confirmClassName: 'button',
        showConfirm: false,
        onConfirm: null,
        clickConfirmOnPressEnter: false,
        style: null,
        ...options
    };

    let _modalElement = null;
    let _modalContainer = null;
    let _closeElement = null;
    let _contentElement = null;
    let _isClosing = false;

    const open = () => {
        if (_modalElement === null) {
            //render element
            _render();
        }
        return _contentElement;
    }

    const close = () => {

        if (!_isClosing) {
            _modalElement.addEventListener('animationend', function () {
                document.body.removeChild(_modalElement);
                destroyDOMElements();
                _isClosing = false;
            });

            _modalContainer.className = _modalContainer.className + ' on-close';
            _modalElement.className = _modalElement.className + ' on-close';

            if (_options.onClose) {
                _options.onClose();
            }
            _isClosing = true;
        }

    }

    const destroyDOMElements = () => {
        if (_contentElement) {
            _contentElement.style.display = 'none';
            document.body.appendChild(_contentElement);
        }
        _modalElement = null;
        _modalContainer = null;
        _closeElement = null;
        _contentElement = null;
    }

    const onConfirm = () => {

        if (_options.onConfirm) {
            _options.onConfirm();
        }

        close();
    }

    const _addTitle = (title) => {
        const titleElement = document.createElement('h2');
        titleElement.className = 'tnp-modal-title';
        titleElement.innerText = title;

        _modalContainer.appendChild(titleElement);
    }

    const _addCloseButton = () => {
        const closeEl = document.createElement('div');
        closeEl.className = 'tnp-modal-close';
        closeEl.innerText = '×';

        _modalContainer.appendChild(closeEl);

        closeEl.addEventListener('click', function (e) {
            e.stopPropagation();
            close();
        });
    }

    const _render = () => {

        _modalContainer = document.createElement('div');
        _modalContainer.className = 'tnp-modal-container';

        if (_options.title && _options.title.length > 0) {

            _addTitle(_options.title);

        }

        if (_options.content && _options.content.length > 0) {

            _contentElement = document.createElement('div');
            _contentElement.className = 'tnp-modal-content';
            _contentElement.innerHTML = _options.content;
            _modalContainer.appendChild(_contentElement);

        } else if (_options.contentSelector && _options.contentSelector.length > 0) {

            _contentElement = document.querySelector(_options.contentSelector);
            _contentElement.style.display = _contentElement.style.display === 'none' ? 'block' : _contentElement.style.display;
            _modalContainer.appendChild(_contentElement);

        } else {

            _contentElement = document.createElement('div');
            _contentElement.className = 'tnp-modal-content';
            _modalContainer.appendChild(_contentElement);

        }

        if (_options.showClose) {
            _addCloseButton();
        }

        if (_options.showConfirm) {

            const confirmContainerEl = document.createElement('div');
            confirmContainerEl.className = 'tnp-modal-confirm';

            const confirmEl = document.createElement('button');
            confirmEl.className = _options.confirmClassName || 'button-secondary';
            confirmEl.innerText = _options.confirmText || 'CONFIRM';

            confirmEl.addEventListener('click', onConfirm);

            if (_options.clickConfirmOnPressEnter) {
                document.addEventListener('keyup', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        confirmEl.click();
                    }
                })
            }

            confirmContainerEl.appendChild(confirmEl);
            _modalContainer.appendChild(confirmContainerEl);

        }

        if (_options.style) {
            for (const _styleProperty in _options.style) {
                if (_modalContainer.style && typeof (_modalContainer.style[_styleProperty]) !== "undefined") {
                    _modalContainer.style[_styleProperty] = _options.style[_styleProperty];
                }
            }
        }

        if (_options.backgroundColor) {
            _modalContainer.style.backgroundColor = _options.backgroundColor;
        }

        if (_options.height) {
            _modalContainer.style.height = _options.backgroundColor;
        }


        _modalElement = document.createElement('div');
        _modalElement.className = 'tnp-modal open';

        if (_options.closeWhenClickOutside) {
            //Close modal if clicked outside modal
            _modalElement.addEventListener('click', function (event) {
                if (!event.target.closest('.' + _modalContainer.className)) {
                    close();
                }
            });
        }

        _modalElement.appendChild(_modalContainer);
        document.body.appendChild(_modalElement);

    }

    if (_options.triggerSelector && _options.triggerSelector.length > 0) {
        const _triggerElement = document.querySelector(_options.triggerSelector);
        _triggerElement.addEventListener('click', open);
    }

    return {
        open,
        close
    }

};

jQuery(function() {
window.TNPModal = TNPModal;
});

const TNPModal2 = (function () {
    'use strict'

    var modalClass = '.tnp-modal2';
    var dataModalTriggerSelector = 'data-tnp-modal-target';
    var dataCloseModalTriggerSelector = 'data-tnp-modal-close';

    class TNPModalx {

        constructor() {

            var self = this;
            var triggers = document.querySelectorAll(`[${dataModalTriggerSelector}]`);

            //Inizializzo i trigger di apertura delle modali
            self._forEach(triggers, function (index, item) {

                var modalTriggerSelector = item.getAttribute(dataModalTriggerSelector);

                item.addEventListener('click', function (e) {
                    self.open(modalTriggerSelector);
                });

            });

            //Inizializzo i trigger di chiusura delle modali
            var closeModalTriggersEl = document.querySelectorAll(`[${dataCloseModalTriggerSelector}]`);
            self._forEach(closeModalTriggersEl, function (index, closeTriggerEl) {
                closeTriggerEl.addEventListener('click', function (e) {
                    self._closeModalElement(e.target.closest(modalClass));
                });
            });

        }

        open(modalSelector) {
            var self = this;
            var modalEl = document.querySelector(modalSelector);

            const showModalEvent = new Event('show.tnp.modal');
            modalEl.dispatchEvent(showModalEvent);

            modalEl.classList.add('open');

            modalEl.addEventListener('click', function (e) {
                if (!e.target.closest('.tnp-modal2__content')) {
                    self._closeModalElement(modalEl);
                }
            });
        }

        close(modalSelector) {
            var modalEl = document.querySelector(modalSelector);
            this._closeModalElement(modalEl);
        }

        _closeModalElement(modal) {
            const hideModalEvent = new Event('hide.tnp.modal');
            modal.dispatchEvent(hideModalEvent);

            modal.classList.add('on-close');

            modal.addEventListener('animationend', function () {
                modal.classList.remove('open');
                modal.classList.remove('on-close');

                const hiddenModalEvent = new Event('hidden.tnp.modal');
                modal.dispatchEvent(hiddenModalEvent);
            }, {once: true});
        }

        _forEach(array, callback, scope) {
            for (var i = 0; i < array.length; i++) {
                callback.call(scope, i, array[i]);
            }
        }
        ;
    }

    return new TNPModalx();
});

jQuery(function() {
window.TNPModal2 = TNPModal2();
});
const TnpToast = (function () { //Module pattern mi permette di rendere private le DEFAULT_OPTIONS e funzione di _render
    'use strict';
    const DEFAULT_OPTIONS = {
        duration: 2000,
        position: 'bottom right',
        wrapperPadding: '20px'
    };

    //Constructor function (mi permette di creare uno scope)
    function TnpToast(options) {

        this._options = Object.assign({}, DEFAULT_OPTIONS, options);
        this._mainWrapperElement = null;

        this._render = function (message, type) {

            if (!this._mainWrapperElement) {
                this._createMainWrapper();
            }

            const columnDirection = this._getNotificationColumnDirectionClassName();

            const notificationElement = document.createElement('div');
            notificationElement.className = `notification notification-${type} ${columnDirection}` + ' ' + this._getNotificationShowAnimationClassName();
            notificationElement.append(message);

            this._mainWrapperElement.appendChild(notificationElement);

            setTimeout(() => {
                this._removeNotification(notificationElement)
            }, this._options.duration);

        }

        this._removeNotification = function (notificationElement) {
            notificationElement.className = notificationElement.className + ' ' + this._getNotificationRemoveAnimationClassName();
            setTimeout(() => {
                this._mainWrapperElement.removeChild(notificationElement);
            }, 1000);
        }

        this._createMainWrapper = function () {
            this._mainWrapperElement = document.createElement('div');
            this._mainWrapperElement.className = 'tnp-toast-main-wrapper';
            this._mainWrapperElement.style.padding = this._options.wrapperPadding;

            const alignments = this._getFlexboxAlignments();
            for (let alignmentProperty of Object.keys(alignments)) {
                this._mainWrapperElement.style[alignmentProperty] = alignments[alignmentProperty];
            }

            const columnDirection = this._getNotificationColumnDirectionClassName();
            if (columnDirection === 'top-to-bottom') {
                this._mainWrapperElement.style.flexDirection = 'column-reverse';
            }

            document.body.appendChild(this._mainWrapperElement);
        }

        this._getFlexboxAlignments = function () {
            const position = this._options.position;
            const spatialPositions = position.split(' ');
            const flexAlignments = {}
            for (let pos of spatialPositions) {
                if (pos === 'top') {
                    flexAlignments.justifyContent = 'flex-end'; //poi aggiungo flex-direction: column-reverse;
                } else if (pos === 'bottom') {
                    flexAlignments.justifyContent = 'flex-end';
                } else if (pos === 'left') {
                    flexAlignments.alignItems = 'flex-start';
                } else if (pos === 'right') {
                    flexAlignments.alignItems = 'flex-end';
                }
            }
            return flexAlignments;
        }

        this._getNotificationColumnDirectionClassName = function () {
            const position = this._options.position;

            return position.includes('top') ? 'top-to-bottom' : 'bottom-to-top';
        }

        this._getNotificationShowAnimationClassName = function () {
            const position = this._options.position;

            return position.includes('top') ? 'push-down' : 'push-up';
        }

        this._getNotificationRemoveAnimationClassName = function () {
            const position = this._options.position;

            return position.includes('top') ? 'pop-up' : 'pop-down';
        }

    }

    TnpToast.prototype.error = function (message) {
        this._render(message, 'error');
    }

    TnpToast.prototype.success = function (message) {
        this._render(message, 'success');
    }

    TnpToast.prototype.info = function (message) {
        this._render(message, 'info');
    }

    TnpToast.prototype.warning = function (message) {
        this._render(message, 'warning');
    }

    return TnpToast;

})();

window.TnpToast = TnpToast;

/*
 //ESEMPIO UTILIZZO API TnpToast
 
 const toastTop = new TnpToast({duration: 5000, position: 'bottom right', wrapperPadding: '70px 20px'});
 
 setTimeout(function () {
 toastTop.info('messaggio di info');
 }, 3000);
 
 setTimeout(function () {
 toastTop.error('messaggio di errore');
 }, 5000);
 
 */




jQuery.cookie = function (name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

function tnp_toggle_schedule() {
    jQuery("#tnp-schedule-button").toggle();
    jQuery("#tnp-schedule").toggle();
}

function tnp_select_toggle(s, t) {
    if (s.value == 1) {
        jQuery("#options-" + t).show();
    } else {
        jQuery("#options-" + t).hide();
    }
}

/*
 * Used by the date field of NewsletterControls
 */
function tnp_date_onchange(field) {
    let id = field.id.substring(0, field.id.lastIndexOf('_'));
    let base_field = document.getElementById('options-' + id);
    let year = document.getElementById(id + '_year');
    let month = document.getElementById(id + '_month');
    let day = document.getElementById(id + '_day');
    if (year.value === '' || month.value === '' || day.value === '') {
        base_field.value = 0;
    } else {
        base_field.value = new Date(year.value, month.value, day.value, 12, 0, 0).getTime() / 1000;
    }
    //this.form.elements['options[" . esc_attr($name) . "]'].value = new Date(document.getElementById('" . esc_attr($name) . "_year').value, document.getElementById('" . esc_attr($name) . "_month').value, document.getElementById('" . esc_attr($name) . "_day').value, 12, 0, 0).getTime()/1000";
}

window.onload = function () {
    jQuery('.tnp-counter-animation').each(function () {
        var _this = jQuery(this);

        var val = null;
        if (!isFloat(_this.text())) {
            val = {
                parsed: parseInt(_this.text()),
                rounded: function (value) {
                    return Math.ceil(value);
                }
            };

            if (_this.hasClass('percentage')) {
                _this.css('min-width', '60px');
            }
        } else {
            val = {
                parsed: parseFloat(_this.text()),
                rounded: function (value) {
                    return value.toFixed(1);
                }
            };
        }

        jQuery({counter: 0}).animate({counter: val.parsed}, {
            duration: 1000,
            easing: 'swing',
            step: function () {
                _this.text(val.rounded(this.counter));
            }
        });

        function isFloat(value) {
            return !isNaN(Number(value)) && Number(value).toString().indexOf('.') !== -1;
        }

    });

    (function targetinFormOnChangeHandler() {

        if (isNewsletterOptionsPage()) {

            var newsletterStatusScheduleValue = jQuery('#tnp-nl-status .tnp-nl-status-schedule-value');

            jQuery('#newsletter-form').change(function (event) {

                if (isElementInsideTargettingTab(event.target)) {
                    newsletterStatusScheduleValue.text(tnp_translations['save_to_update_counter']);
                }

                function isElementInsideTargettingTab(element) {
                    return jQuery(element).parents('#tabs-options').length === 1
                }

            });
        }

        function isNewsletterOptionsPage() {
            return jQuery("#tnp-nl-status").length
                    && jQuery("#newsletter-form").length;
        }

    })();

};

/**
 * Initialize the color pickers (is invoked on document load and on AJAX forms load in the composer.
 * https://seballot.github.io/spectrum/
 */
function tnp_controls_init() {
    jQuery(".tnpc-color").spectrum({
        type: 'color',
        allowEmpty: true,
        showAlpha: false,
        showInput: true,
        preferredFormat: 'hex'
    });
}

function tnp_fields_media_mini_select(el) {
    event.preventDefault();

    let name = jQuery(el).data("name");

    let tnp_uploader = wp.media({
        title: "Select an image",
        button: {
            text: "Select"
        },
        multiple: false
    }).on("select", function () {
        let media = tnp_uploader.state().get("selection").first();
        let $field = jQuery("#" + name + "_id");
        $field.val(media.id);
        $field.trigger("change");

        var img_url = media.attributes.url;
        if (typeof media.attributes.sizes.thumbnail !== "undefined")
            img_url = media.attributes.sizes.thumbnail.url;
        document.getElementById(name + "_img").src = img_url;
    }).open();
}

function tnp_fields_url_select(el) {
    event.preventDefault();

    let field_id = jQuery(el).data("field");

    let tnp_uploader = wp.media({
        title: "Select an image",
        button: {
            text: "Select"
        },
        multiple: false
    }).on("select", function () {
        let media = tnp_uploader.state().get("selection").first();
        let $field = jQuery("#" + field_id);
        $field.val(media.attributes.url);
        $field.trigger("change");
    }).open();
}

function tnp_fields_media_mini_remove(name) {
    event.preventDefault();
    event.stopPropagation();
    let $field = jQuery("#" + name + "_id");
    $field.val("");
    $field.trigger("change");
    document.getElementById(name + "_img").src = "";
}

function tnp_lists_toggle(e) {
    jQuery('#' + e.id + '-notes > div').hide();
    jQuery('#' + e.id + '-notes .list_' + e.value).show();
}
