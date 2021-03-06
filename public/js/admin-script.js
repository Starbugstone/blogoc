// Toggle the side navigation
$("#sidebarToggle").click(function (e) {
    e.preventDefault();
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
});

function setupTinymce($selector) {
    //getting our base URL
    var url = window.location.origin;
    tinymce.init({
        selector: $selector,

        /* display statusbar */
        statubar: false,
        branding: false,

        /* theme of the editor */
        theme: "modern",
        skin: "lightgray",

        /* width and height of the editor */
        min_height: 450,

        relative_urls: false,
        remove_script_host: true,
        document_base_url: url, //we want to call from the base url
        convert_urls: true,
        //document_base_url: "/",

        /* plugin */
        plugins: [
            "code pagebreak image hr lists preview fullscreen textcolor emoticons link"
        ],

        /* toolbar */
        menubar: false,
        toolbar: [
            "bold italic underline strikethrough link | formatselect forecolor backcolor | bullist numlist | alignleft aligncenter alignright alignjustify",
            " undo redo | emoticons image hr pagebreak | alignnone removeformat | fullscreen code preview"
        ],

        /* Page Break */
        pagebreak_separator: "<!-- EndOfExcerptBlogOc -->",

        /* Images uploading
        https://www.codexworld.com/tinymce-upload-image-to-server-using-php/
        */
        images_upload_url: "/ajax/image-upload/tinymce-upload",
        automatic_uploads: false, /* Not sure about this */
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;

            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;

            xhr.open("POST", "/ajax/image-upload/tinymce-upload");
            xhr.setRequestHeader("Csrftoken", $("meta[name=\"csrf_token\"]").attr("content")); //seneding our csrf token.
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest"); //seneding our XML header.

            xhr.onload = function () {
                var json;

                if (xhr.status !== 200) {
                    failure("HTTP Error: " + xhr.status);
                    return;
                }

                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != "string") {
                    failure("Invalid JSON: " + xhr.responseText);
                    return;
                }

                success(json.location);
            };

            formData = new FormData();
            formData.append("file", blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        },

    });
}