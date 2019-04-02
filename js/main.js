
var Fortscript = {
    get sendBtn() {
        return document.getElementById("Post-send");
    },
    get sendArea() {
        return document.getElementById("Post-area");
    },
    get commentBtn() {
        return document.getElementById("Comment-send");
    },
    get commentArea() {
        return document.getElementById("Comment-area");
    },
    get modalDialog() {
        return document.getElementById("Modal");
    },
    get feedArea() {
        return document.getElementById("Feed");
    },
    get morePostsCard() {
        return document.getElementById("Card-MoreNotes");
    },
    get noPostsCard() {
        return document.getElementById("Card-NotFound");
    },
    get groupPostMenu() {
        return document.getElementById("GroupSelector-Menu");
    },
    get mainForm() {
        return document.getElementById("main-form");
    },
    get searchBar() {
        return document.getElementById("Search");
    },
    get colorContainer() {
        return document.getElementById("ColorContainer");
    },
    findGetParameter: function (parameterName, raw = true) {
        var result = null,
            tmp = [];
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            tmp = items[index].split("=");
            if (tmp[0] === parameterName) {
                if (raw) {
                    result = decodeURIComponent(tmp[1]);
                } else {
                    result = decodeURIComponent(tmp[1]).replace('+', ' ');
                }
            }
        }
        return result;
    },
    removeParameter: function (key, sourceURL) {
        var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }
            rtn = rtn + "?" + params_arr.join("&");
        }
        return rtn;
    },
    postCount: 10,
    init: function () {
        if (Fortscript.sendBtn) {
            Fortscript.sendBtn.addEventListener("click", Fortscript.sendPost, false);
            Fortscript.sendArea.value = "";
        }
        if (Fortscript.commentBtn) {
            Fortscript.commentBtn.addEventListener("click", Fortscript.sendComment, false);
            Fortscript.commentArea.value = "";
        }
        if (Fortscript.mainForm) {
            Fortscript.mainForm.addEventListener("submit", Fortscript.sendMaterial);
        }
        if (Fortscript.searchBar) {
            Fortscript.searchBar.value = Fortscript.findGetParameter("q", false);
        }
        document.documentElement.style.setProperty("--page-background-color", Fortscript.colorContainer.getAttribute("page"));
        document.documentElement.style.setProperty("--header-text-color", Fortscript.colorContainer.getAttribute("accent"));
        document.documentElement.style.setProperty("--header-background-color", Fortscript.colorContainer.getAttribute("header"));
        
        function hexToRgb(hex) {
            // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
            var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hex = hex.replace(shorthandRegex, function(m, r, g, b) {
                return r + r + g + g + b + b;
            });

            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result;
        }
        
        let color = hexToRgb(getComputedStyle(document.documentElement).getPropertyValue('--page-background-color'));
        let r = parseInt(color[1], 16);
        let g = parseInt(color[2], 16);
        let b = parseInt(color[3], 16);
        let luminance = (2 * r + 5 * g + b) / 8;
        if (luminance <= 128) {
            document.documentElement.setAttribute('dark', true);
        }
        // Close the drop-down menu when something else was clicked
        document.addEventListener("click", function (event) {
            if (!event.target.matches('.dropdown-button')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                var i;
                for (i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('visible')) {
                        openDropdown.classList.remove('visible');
                    }
                }
            }
        });

        // Initialize the modal
        if (Fortscript.modalDialog) {
            let span = document.getElementsByClassName("close")[0];
            span.addEventListener("click", function () {
                Fortscript.modalDialog.style.display = "none";
            }, false);
            document.addEventListener("click", function (event) {
                if (event.target == Fortscript.modalDialog) {
                    Fortscript.modalDialog.style.display = "none";
                }
            }, false);
        }
    },
    showDropDown: function (id) {
        document.getElementById("Profile-Dropdown").classList.toggle("visible");
    },
    openModal: function (value, customEvent) {
        if (Fortscript.modalDialog) {
            // Init
            let modalText = document.getElementById("ModalText");
            modalText.innerHTML = value;
            Fortscript.modalDialog.style.display = "block";
            // Custom event
            if (customEvent) {
                let span = document.getElementsByClassName("close")[0];
                span.addEventListener("click", function () {
                    customEvent();
                }, false);
                document.addEventListener("click", function (event) {
                    if (event.target == Fortscript.modalDialog) {
                        customEvent();
                    }
                }, false);
            }
        }
    },
    sendPost: function () {
        let request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                Fortscript.openModal(this.responseText, function () {
                    location.reload();
                });
            }
        };

        let groupid = Fortscript.groupPostMenu.selectedIndex + 1;
        let params = "action=new&content=" + Fortscript.sendArea.value;
        params += "&group=" + groupid;
        request.open("POST", "/common/post.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(params);
    },
    sendComment: function () {
        let request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                Fortscript.openModal(this.responseText, function () {
                    location.reload();
                });
            }
        };

        let params = "action=new_comment&content=" + Fortscript.commentArea.value +
                     "&post=" + Fortscript.findGetParameter("id");
        request.open("POST", "/common/post.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(params);
    },
    deletePost: function (e) {
        let request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                Fortscript.openModal(this.responseText, function () {
                    location.reload();
                });
            }
        };

        let postid = e.target.parentElement.getAttribute("postid");
        let params = "action=delete&post=" + postid;
        request.open("POST", "/common/post.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(params);
    },
    deleteComment: function (e) {
        let request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                Fortscript.openModal(this.responseText, function () {
                    location.reload();
                });
            }
        };

        let commentid = e.target.parentElement.getAttribute("commentid");
        let params = "action=delete_comment&post=" + commentid;
        request.open("POST", "/common/post.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(params);
    },
    editPost: function (e) {
        let request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                Fortscript.openModal(this.responseText);
            }
        };

        cachedPostID = e.target.parentElement.getAttribute("postid");
        let params = "action=get_edit_modal&post=" + cachedPostID;

        request.open("POST", "/common/post.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(params);
    },
    cachedPostID: null,
    sendEditedPost: function () {
        let request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                Fortscript.openModal(this.responseText, function () {
                    location.reload();
                });
            }
        };

        let value = document.getElementById("Edit-area").value;

        let params = "action=edit&post=" + cachedPostID;
        params += "&content=" + value;
        request.open("POST", "/common/post.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(params);
    },
    sendMaterial: function (e) {
        e.preventDefault();
        
        let request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                Fortscript.openModal(this.responseText, function () {
                    location.assign(Fortscript.removeParameter("action", location.href));
                });
            }
        };
        
        let formData = new FormData(Fortscript.mainForm);
        request.open("POST", "/common/post.php");
        request.send(formData);
    },
    addPosts: function (page) {
        let request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                Fortscript.feedArea.innerHTML += this.responseText;
                if (!Fortscript.noPostsCard) {
                    Fortscript.feedArea.appendChild(Fortscript.morePostsCard);
                } else {
                    Fortscript.feedArea.removeChild(Fortscript.morePostsCard);
                }
				// FIXME: THIS IS A CRAPPY WORKAROUND!
                Fortscript.init();
            }
        };

        let params = "action=get_posts&";

        switch (page) {
            case "dashboard":
                params += "offset=" + Fortscript.postCount;
                break;
            case "groups":
                let groupID = "group=" + Fortscript.feedArea.getAttribute("groupid");
                params += groupID + "&show-category=false&offset=" + Fortscript.postCount;
                break;
            case "profile":
                let userID = "user=" + Fortscript.feedArea.getAttribute("userid");
                params += userID + "&offset=" + Fortscript.postCount;
                break;
            default:
                return;
        }
        Fortscript.postCount += 10;
        request.open("POST", "/common/post.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(params);
    }
};

document.addEventListener("DOMContentLoaded", Fortscript.init, false);
