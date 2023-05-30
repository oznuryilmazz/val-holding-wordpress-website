(function (blocks, editor, element, components) {


    const el = element.createElement;
    const {registerBlockType} = blocks;
    const { RichText, InspectorControls, withColors, PanelColorSettings, getColorClassName, AlignmentToolbar, BlockControls } = editor;
    const { Fragment } = element;
    const { TextControl, RadioControl, Panel, PanelBody, PanelRow, SelectControl, RangeControl } = components;
    const colorSamples = [
        {
            name: 'GREEN SEA',
            slug: 'GREENSEA',
            color: '#16A085'
        },
        {
            name: 'NEPHRITIS',
            slug: 'NEPHRITIS',
            color: '#27AE60'
        },
        {
            name: 'BELIZE HOLE',
            slug: 'BELIZEHOLE',
            color: '#2980B9'
        },
        {
            name: 'WISTERIA',
            slug: 'WISTERIA',
            color: '#8E44AD'
        },
        {
            name: 'MIDNIGHT BLUE',
            slug: 'MIDNIGHTBLUE',
            color: '#2C3E50'
        },
        {
            name: 'ORANGE',
            slug: 'ORANGE',
            color: '#F39C12'
        },
        {
            name: 'ALIZARIN',
            slug: 'ALIZARIN',
            color: '#E74C3C'
        },
        {
            name: 'WHITE',
            slug: 'WHITE',
            color: '#FFFFFF'
        },
        {
            name: 'CLOUDS',
            slug: 'CLOUDS',
            color: '#ECF0F1'
        },
        {
            name: 'ASBESTOS',
            slug: 'ASBESTOS',
            color: '#7F8C8D'
        }
    ];

    registerBlockType('tnp/minimal', {
        title: 'Newsletter subscription form',
        icon: 'email',
        category: 'common',
        keywords: ['newsletter', 'subscription', 'form'],
        attributes: {
            formtype: {type: 'string', default: 'minimal'},
            content: { type: 'array', source: 'children', selector: 'p', default: 'Subscribe to our newsletter!'},
            list_ids: { type: 'string' },
            rowColor: { type: 'string'},
            customRowColor: { type: 'string'},
            textColor: { type: 'string'},
            customTextColor: { type: 'string'},
            buttonColor: { type: 'string'},
            customButtonColor: { type: 'string'},
            padding: {type: 'integer', default: 20},
            alignment: { type: 'string'}
        },

        edit: withColors('rowColor', 'textColor', 'buttonColor')(function (props) {

            function onChangeContent( newContent ) {
                props.setAttributes( { content: newContent } );
            }

            function onChangeAlignment( newAlignment ) {
                props.setAttributes( { alignment: newAlignment } );
            }

            return el( Fragment, {},
                el( InspectorControls, {},

                    // 1st Panel - Form Settings
                    el( PanelBody, { title: 'Form Settings', initialOpen: true },

                        /* Form type */
                        el( RadioControl,
                            {
                                label: 'Form type',
                                options : [
                                    { label: 'Minimal', value: 'minimal' },
                                    { label: 'Full', value: 'full' },
                                ],
                                onChange: ( value ) => {
                                    props.setAttributes( { formtype: value } );
                                },
                                selected: props.attributes.formtype
                            }
                        ),

                        /* Lists field */
                        el( PanelRow, {},
                            el( TextControl,
                                {
                                    label: 'Lists IDs (comma separated)',
                                    onChange: ( value ) => {
                                        props.setAttributes( { list_ids: value } );
                                    },
                                    value: props.attributes.list_ids
                                }
                            )
                        )
                    ),

                        /* Style */
                        el( PanelColorSettings, {
                            title: 'Style',
                            colorSettings: [
                                {
                                    colors: colorSamples, // here you can pass custom colors
                                    value: props.rowColor.color,
                                    label: 'Background color',
                                    onChange: props.setRowColor,
                                },
                                {
                                    colors: colorSamples, // here you can pass custom colors
                                    value: props.textColor.color,
                                    label: 'Text color',
                                    onChange: props.setTextColor,
                                },
                                {
                                    colors: colorSamples, // here you can pass custom colors
                                    value: props.buttonColor.color,
                                    label: 'Button color',
                                    onChange: props.setButtonColor,
                                }
                            ]
                        }),

                    el( RangeControl,
                        {
                            label: 'Padding',
                            min: 0,
                            max: 100,
                            onChange: ( value ) => {
                                props.setAttributes( { padding: value } );
                            },
                            value: props.attributes.padding
                        }
                    )

                ),

            el(
                "div",
                {style: {backgroundColor: props.rowColor.color, color: props.textColor.color, padding: props.attributes.padding, textAlign: props.attributes.alignment}},
                el(
                    BlockControls,
                    { key: 'controls' },
                    el(
                        AlignmentToolbar,
                        {
                            value: props.attributes.alignment,
                            onChange: onChangeAlignment
                        }
                    )
                ),
                el(RichText,
                {
                    tagName: 'p',
                    format: 'string',
                    onChange: onChangeContent,
                    value: props.attributes.content,
                    // formattingControls: [ 'bold' ]
                }),
                el('div',
                    {style: {backgroundColor: 'lightGrey', margin: '20px', padding: '5px',
                            fontFamily: '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif'}},
                    el('svg',
                        {
                            width: 20,
                            height: 20
                        },
                        wp.element.createElement( 'path',
                            {
                                d: "M6 14H4V6h2V4H2v12h4M7.1 17h2.1l3.7-14h-2.1M14 4v2h2v8h-2v2h4V4"
                            }
                        )
                    ),
                    ' Newsletter Form'
                ),
                ))
        }),
        save: function (props) {

            var rowClass = getColorClassName( 'row-color', props.attributes.rowColor );
            var textClass = getColorClassName( 'text-color', props.attributes.textColor );
            var buttonClass = getColorClassName( 'button-color', props.attributes.buttonColor );

            formtype_attr = "";
            if (props.attributes.formtype != "full") {
                formtype_attr = " type=\"minimal\"";
            }

            lists_attr = "";
            if (props.attributes.list_ids) {
                lists_attr = " lists=\"" + props.attributes.list_ids + "\"";
            }

            button_color_attr = "";
            button_color = buttonClass ? undefined : props.attributes.customButtonColor;
            if (button_color) {
                button_color_attr = " button_color=\"" + button_color + "\"";
            }

            var formStyles = {
                backgroundColor: rowClass ? undefined : props.attributes.customRowColor,
                color: textClass ? undefined : props.attributes.customTextColor,
                padding: props.attributes.padding,
                textAlign: props.attributes.alignment
            };

            return (
                el('div', {style: formStyles},
                    el( RichText.Content, {
                        tagName: 'p',
                        value: props.attributes.content
                    }),
                el(
                "div",
                {},
                "[newsletter_form" + formtype_attr + lists_attr + button_color_attr + "]"
            )));
        }
    });

})(
    window.wp.blocks,
    window.wp.blockEditor,
    window.wp.element,
    window.wp.components,
);
