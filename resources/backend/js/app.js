import 'tinymce/tinymce';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/models/dom';

import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/table';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/code';

document.addEventListener('livewire:load', function () {
    tinymce.init({
        selector: '#tinymce-editor',
        plugins: 'link image table lists code',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image table | code',
        skin: 'oxide',
        setup: function (editor) {
            editor.on('change keyup', function () {
                const component = window.Livewire.find(
                    document.querySelector('#tinymce-editor').closest('[wire\\:id]').getAttribute('wire:id')
                );
                if (component) {
                    component.set('content_txt', editor.getContent());
                }
            });
        }
    });
});
