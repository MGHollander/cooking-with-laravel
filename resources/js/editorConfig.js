import {
  Bold,
  Essentials,
  Heading,
  HeadingButtonsUI,
  Italic,
  Link,
  List,
  Paragraph,
  Strikethrough,
  Underline
} from 'ckeditor5';

const licenseKey = import.meta.env.VITE_CKEDITOR_LICENSE_KEY;

export const summaryEditorConfig = {
  licenseKey: licenseKey,
  plugins: [Essentials, Bold, Italic, Link, Paragraph, Strikethrough, Underline],

  toolbar: {
    items: ["bold", "italic", "underline", "strikeThrough", "|", "link"],
  },
};

export const instructionsEditorConfig = {
  licenseKey: licenseKey,
  plugins: [Essentials, Bold, Italic, Link, List, Paragraph, Strikethrough, Underline, Heading, HeadingButtonsUI],
  heading: {
    options: [{model: "heading3", view: "h3", title: "Heading"}],
  },
  toolbar: {
    items: [
      "bold",
      "italic",
      "underline",
      "strikeThrough",
      "|",
      "bulletedList",
      "numberedList",
      "|",
      "heading3",
      "|",
      "link",
    ],
  },
};
