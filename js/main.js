
var Fortscript = {
    get sendTextArea() {
        return document.getElementById("Post-area");
    },
    get sendTextBtn() {
        return document.getElementById("Post-send");
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
    postCount: 10,
    init: function () {
        Fortscript.sendTextBtn.addEventListener("click", Fortscript.sendPost, false);
        Fortscript.sendTextArea.value = "";

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
                Fortscript.openModal(this.responseText, function () { location.reload(); } );
            }
        };

        request.open("POST", "/common/post.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("action=new&group=1&content=" + Fortscript.sendTextArea.value );
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
