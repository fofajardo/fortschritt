
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
    findGetParameter: function (parameterName) {
        var result = null,
            tmp = [];
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            tmp = items[index].split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        }
        return result;
    },
    postCount: 10,
    init: function () {
        if (Fortscript.sendBtn) {
            Fortscript.sendBtn.addEventListener("click", Fortscript.sendPost, false);
            Fortscript.sendArea.value = "";
        }
        if (Fortscript.commentBtn) {
            Fortscript.commentBtn.addEventListener("click", Fortscript.sendComment, false);
            Fortscript.commentArea = "";
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
