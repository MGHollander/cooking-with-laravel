import { Bold, Italic, Strikethrough, Underline } from "@ckeditor/ckeditor5-basic-styles";
import { Essentials } from "@ckeditor/ckeditor5-essentials";
import { Heading, HeadingButtonsUI } from "@ckeditor/ckeditor5-heading";
import { Link } from "@ckeditor/ckeditor5-link";
import { List } from "@ckeditor/ckeditor5-list";
import { Paragraph } from "@ckeditor/ckeditor5-paragraph";

export const summaryEditorConfig = {
  plugins: [Essentials, Bold, Italic, Link, Paragraph, Strikethrough, Underline],

  toolbar: {
    items: ["bold", "italic", "underline", "strikeThrough", "|", "link"],
  },
};

export const instructionsEditorConfig = {
  plugins: [Essentials, Bold, Italic, Link, List, Paragraph, Strikethrough, Underline, Heading, HeadingButtonsUI],
  heading: {
    options: [{ model: "heading3", view: "h3", title: "Heading" }],
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
