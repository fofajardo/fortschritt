var Fortsettings = {
    get accent_picker() {
        return document.getElementById("Picker-Accent-Color");
    },
    get background_picker() {
        return document.getElementById("Picker-Background-Color");
    },
    get header_picker() {
        return document.getElementById("Picker-Header-Color");
    },
    init: function () {
        if (Fortsettings.background_picker) {
            if (Fortsettings.background_picker.value == "") {
                Fortsettings.background_picker.value = "#F2F2F2";
            }
            let picker = new CP(Fortsettings.background_picker, "click");

            picker.on("change", function (color) {
                Fortsettings.background_picker.value = '#' + color;
                document.documentElement.style.setProperty("--page-background-color", Fortsettings.background_picker.value);
                Fortscript.determineUILuminosity();
            });
        }
        if (Fortsettings.accent_picker) {
            if (Fortsettings.accent_picker.value == "") {
                Fortsettings.accent_picker.value = "#1855a5";
            }
            let picker = new CP(Fortsettings.accent_picker, "click");

            picker.on("change", function (color) {
                Fortsettings.accent_picker.value = '#' + color;
                document.documentElement.style.setProperty("--header-text-color", Fortsettings.accent_picker.value);
            });
        }
        if (Fortsettings.header_picker) {
            if (Fortsettings.accent_picker.value == "") {
                Fortsettings.header_picker.value = "#fff";
            }
            let picker = new CP(Fortsettings.header_picker, "click");

            picker.on("change", function (color) {
                Fortsettings.header_picker.value = '#' + color;
                document.documentElement.style.setProperty("--header-background-color", Fortsettings.header_picker.value);
            });
        }
    }
};
Fortsettings.init();