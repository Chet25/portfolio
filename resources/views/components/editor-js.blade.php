@props([
    'name' => 'content',
    'uploadUrl' => '/api/editor/upload-image',
    'linkFetchUrl' => '/api/editor/fetch-link'
])

<div 
    x-data="{
        editor: null,
        content: @entangle($attributes->wire('model')),
        init() {
            this.$nextTick(() => {
                this.editor = window.initEditorJS(
                    'editorjs-{{ $name }}',
                    this.content || '',
                    '{{ $uploadUrl }}',
                    '{{ $linkFetchUrl }}'
                );
            });

            window.addEventListener('editor-content-updated', (e) => {
                this.content = e.detail.content;
            });
        }
    }"
    x-init="init()"
>
    <div 
        id="editorjs-{{ $name }}" 
        class="min-h-[400px] rounded-lg p-4 bg-white text-black"
    ></div>
</div>

<style>
    .ce-block__content,
    .ce-toolbar__content {
        max-width: 70% !important;
    }

    .ce-block {
        margin: 0 !important;
        padding: 4px 0 !important;
    }

    .codex-editor__redactor {
        padding-bottom: 20px !important;
    }

    .codex-editor {
        /* min-height: 400px !important; */
        height: auto !important;
        overflow: visible !important;
    }

    .image-tool__image {
        max-width: 100% !important;
        height: auto !important;
    }
</style>
