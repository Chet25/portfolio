import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Code from '@editorjs/code';
import Quote from '@editorjs/quote';
import Delimiter from '@editorjs/delimiter';
import InlineCode from '@editorjs/inline-code';
import ImageTool from '@editorjs/image';
import LinkTool from '@editorjs/link';

window.initEditorJS = function (elementId, initialData, uploadUrl, linkFetchUrl, onChangeCallback) {
    let parsedData = {};
    
    if (initialData && initialData.trim() !== '') {
        try {
            parsedData = JSON.parse(initialData);
        } catch (e) {
            console.warn('Could not parse initial Editor.js data:', e);
            parsedData = {};
        }
    }

    const editor = new EditorJS({
        holder: elementId,
        placeholder: 'Start writing your blog post...',
        data: parsedData,
        tools: {
            header: {
                class: Header,
                config: {
                    placeholder: 'Enter a heading',
                    levels: [2, 3, 4],
                    defaultLevel: 2
                }
            },
            list: {
                class: List,
                inlineToolbar: true,
                config: {
                    defaultStyle: 'unordered'
                }
            },
            code: {
                class: Code,
                config: {
                    placeholder: 'Enter code here...'
                }
            },
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: 'Enter a quote',
                    captionPlaceholder: 'Quote author'
                }
            },
            delimiter: Delimiter,
            inlineCode: {
                class: InlineCode
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: uploadUrl,
                        byUrl: uploadUrl
                    },
                    field: 'image',
                    types: 'image/*',
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                }
            },
            linkTool: {
                class: LinkTool,
                config: {
                    endpoint: linkFetchUrl
                }
            }
        },
        onChange: async () => {
            try {
                const outputData = await editor.save();
                const jsonString = JSON.stringify(outputData);
                
                if (typeof onChangeCallback === 'function') {
                    onChangeCallback(jsonString);
                } else {
                    // Dispatch a custom event for Livewire to listen to
                    window.dispatchEvent(new CustomEvent('editor-content-updated', {
                        detail: { content: jsonString }
                    }));
                }
            } catch (error) {
                console.error('Editor.js save failed:', error);
            }
        }
    });

    return editor;
};
