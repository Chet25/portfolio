<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        wire:ignore
        class="w-full flex justify-center bg-zinc-100/50 dark:bg-zinc-950/50 rounded-2xl p-2 md:p-4"
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            init() {
                if (window.initEditorJS) {
                    window.initEditorJS(
                        'editorjs-{{ $getId() }}',
                        this.state,
                        '{{ route('editor.upload-image') }}',
                        '{{ route('editor.fetch-link') }}',
                        (content) => {
                            this.state = content;
                        }
                    );
                }
            }
        }"
    >
        <div 
            id="editorjs-{{ $getId() }}" 
            class="w-full max-w-4xl min-h-[500px] rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 px-4 py-8 md:px-12 md:py-12 prose prose-zinc dark:prose-invert shadow-sm focus-within:ring-2 focus-within:ring-primary-500/50 transition-all"
        ></div>
    </div>

    <style>
        /* Center and cap the block width for readability */
        .ce-block__content,
        .ce-toolbar__content {
            max-width: 800px !important; 
            margin: 0 auto;
        }

        .codex-editor__redactor {
            padding-bottom: 100px !important;
        }

        /* Dark mode enhancements */
        .dark .ce-toolbar__plus,
        .dark .ce-toolbar__settings-btn,
        .dark .ce-popover,
        .dark .ce-inline-toolbar {
            background-color: #18181b !important;
            border-color: #3f3f46 !important;
            color: #f4f4f5 !important;
        }

        .dark .ce-popover__item:hover,
        .dark .ce-inline-tool:hover {
            background-color: #27272a !important;
        }

        /* Smooth out the tool icons */
        .ce-toolbar__plus, .ce-toolbar__settings-btn {
            border-radius: 8px;
        }
    </style>
</x-dynamic-component>