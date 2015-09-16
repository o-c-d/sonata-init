/*

https://github.com/xsolve-pl/xsolve-cookie-acknowledgement-bundle

*/

(function () {
    var COOKIE_BAR = {
        storageItemName: 'ocd.eu_cookie_consent',
        storageItemAcceptedValue: 'accepted',
        storageItemClosedValue: 'closed',
        infoDivID: 'cookie-bar-info',
        infoAcceptLinkID: 'cookie-bar-info-accept',
        infoCloseLinkID: 'cookie-bar-info-close',

        showCookieBar: function () {
            this.cookieBarDiv.style.display = 'block';
        },

        hideCookieBar: function () {
            this.cookieBarDiv.style.display = 'none';
        },

        shouldShowCookieBar: function () {
			if(window.localStorage.getItem(this.storageItemName) !== this.storageItemAcceptedValue && window.localStorage.getItem(this.storageItemName) !== this.storageItemClosedValue) return true ;
			else return false ;
        },

        processCookieBar: function () {
            if (this.shouldShowCookieBar()) {
                this.showCookieBar();
            }
        },

        processCookieAccept: function () {
            window.localStorage.setItem(this.storageItemName, this.storageItemAcceptedValue);
            this.hideCookieBar();
        },

        processCookieClose: function () {
            window.localStorage.setItem(this.storageItemName, this.storageItemClosedValue);
            this.hideCookieBar();
        },

        init: function () {
            var _this = this;

            this.cookieBarDiv = document.getElementById(this.infoDivID),
            this.cookieAcceptButton = document.getElementById(this.infoAcceptLinkID);
            this.cookieCloseButton = document.getElementById(this.infoCloseLinkID);

            this.processCookieBar();
            this.cookieAcceptButton.onclick = function () {
                _this.processCookieAccept();
            };
            this.cookieCloseButton.onclick = function () {
                _this.processCookieClose();
            };
        }
    };

    document.addEventListener("DOMContentLoaded", function() {
        COOKIE_BAR.init();
    });
}());
