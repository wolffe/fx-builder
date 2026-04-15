(function () {
    function getConfig() {
        return window.fxbSettingsFonts || {};
    }

    function getStrings() {
        var config = getConfig();
        return config.strings || {};
    }

    function appendOption(selectEl, value, label, selectedValues) {
        var option = document.createElement("option");
        option.value = value;
        option.textContent = label;
        option.selected = selectedValues.indexOf(value) !== -1;
        selectEl.add(option);
    }

    function initTailSelect(selector, placeholderText) {
        if (!window.tail || typeof window.tail.select !== "function") {
            return;
        }

        if (!document.querySelector(selector)) {
            return;
        }

        tail.select(selector, {
            multiple: true,
            multiSelectAll: true,
            stayOpen: false,
            search: true,
            classNames: "wp4pm-flex-item",
            buttonAll: false,
            buttonNone: false,
            buttonClose: true,
            buttonCloseText: "✕",
            strings: {
                placeholder: placeholderText
            }
        });
    }

    function loadGoogleFonts() {
        var config = getConfig();
        var strings = getStrings();
        var googleSelect = document.getElementById("fxb-font");

        if (!googleSelect) {
            return;
        }

        if (!config.googleApiKey) {
            initTailSelect("#fxb-font", strings.placeholderGoogle || "Select Google Font(s)...");
            return;
        }

        fetch("https://www.googleapis.com/webfonts/v1/webfonts?key=" + encodeURIComponent(config.googleApiKey))
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                var selected = Array.isArray(config.googleFonts) ? config.googleFonts : [];
                var items = Array.isArray(data.items) ? data.items : [];

                items.forEach(function (fontFamily) {
                    appendOption(
                        googleSelect,
                        fontFamily.family,
                        fontFamily.family + " (" + fontFamily.category + ")",
                        selected
                    );
                });

                initTailSelect("#fxb-font", strings.placeholderGoogle || "Select Google Font(s)...");
            })
            .catch(function () {
                initTailSelect("#fxb-font", strings.placeholderGoogle || "Select Google Font(s)...");
            });
    }

    function loadBunnyFonts() {
        var config = getConfig();
        var strings = getStrings();
        var bunnySelect = document.getElementById("fxb-bunny-fonts");

        if (!bunnySelect) {
            return;
        }

        fetch("https://fonts.bunny.net/list")
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                var selected = Array.isArray(config.bunnyFonts) ? config.bunnyFonts : [];

                Object.entries(data).forEach(function (entry) {
                    var fontData = entry[1];
                    appendOption(
                        bunnySelect,
                        fontData.familyName,
                        fontData.familyName + " (" + fontData.category + ")",
                        selected
                    );
                });

                initTailSelect("#fxb-bunny-fonts", strings.placeholderBunny || "Select Bunny Font(s)...");
            })
            .catch(function () {
                initTailSelect("#fxb-bunny-fonts", strings.placeholderBunny || "Select Bunny Font(s)...");
            });
    }

    function onReady(callback) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", callback);
            return;
        }
        callback();
    }

    onReady(function () {
        loadGoogleFonts();
        loadBunnyFonts();
    });
})();
