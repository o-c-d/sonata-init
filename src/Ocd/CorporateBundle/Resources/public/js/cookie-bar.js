/*

https://github.com/xsolve-pl/xsolve-cookie-acknowledgement-bundle

*/

(function () {
    var COOKIE_BAR = {
        storageItemName: 'xsolve.cookie-law-accepted',
        storageItemValue: 'accepted',
        infoDivID: 'cookie-bar-info',
        infoCloseLinkID: 'cookie-bar-info-close',

        showCookieBar: function () {
            this.cookieBarDiv.style.display = 'block';
        },

        hideCookieBar: function () {
            this.cookieBarDiv.style.display = 'none';
        },

        shouldShowCookieBar: function () {
            return window.localStorage.getItem(this.storageItemName) !== this.storageItemValue;
        },

        processCookieBar: function () {
            if (this.shouldShowCookieBar()) {
                this.showCookieBar();
            }
        },

        processCookieAccept: function () {
            window.localStorage.setItem(this.storageItemName, this.storageItemValue);
            this.hideCookieBar();
        },

        init: function () {
            var _this = this;

            this.cookieBarDiv = document.getElementById(this.infoDivID),
            this.cookieAcceptButton = document.getElementById(this.infoCloseLinkID);

            this.processCookieBar();
            this.cookieAcceptButton.onclick = function () {
                _this.processCookieAccept();
            };
        }
    };

    document.addEventListener("DOMContentLoaded", function() {
        COOKIE_BAR.init();
    });
}());
