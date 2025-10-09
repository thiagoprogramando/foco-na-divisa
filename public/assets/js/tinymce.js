tinymce.init({
    selector: '.editor',
    plugins: [
        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons',
        'image', 'link', 'lists', 'media', 'searchreplace', 'table',
        'visualblocks', 'wordcount', 'advlist'
    ],
    toolbar: `
        undo redo | blocks fontfamily fontsize |
        bold italic underline strikethrough forecolor backcolor |
        link image media table mergetags |
        align lineheight |
        checklist numlist bullist indent outdent |
        emoticons charmap |
        removeformat
    `,
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) =>
        respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
});

tinymce.init({
    selector: '.editor-simple',
    plugins: [
        'lists', 'advlist', 'autolink', 'charmap', 'emoticons', 'wordcount'
    ],
    toolbar: `
        undo redo |
        bold italic underline strikethrough |
        forecolor backcolor |
        alignleft aligncenter alignright alignjustify |
        bullist numlist outdent indent |
        removeformat
    `,
    menubar: false,
    statusbar: false,
    branding: false,
    height: 250,
});
