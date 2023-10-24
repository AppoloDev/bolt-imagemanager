import {FileManager} from "filemanager-element";
import "filemanager-element/FileManager.css";

class ImageManager extends HTMLElement {
  connectedCallback() {
    this.filemanager = this.querySelector("file-manager");
    this.imageManagerButton = this.querySelector("button#image-manager");
    this.fileInput = this.querySelector('input[name$="[filename]"]');
    this.altInput = this.querySelector('input[name$="[alt]"]');

    if (this.filemanager && this.imageManagerButton && this.fileInput) {
      FileManager.register();

      this.imageManagerButton.addEventListener('click', () => {
        this.filemanager.removeAttribute('hidden');
      })

      this.querySelector('button#remove').addEventListener('click', () => {
        this.changeValue('');
        if (this.altInput) {
          this.altInput.value = '';
        }
      })

      this.filemanager.addEventListener("close", () => {
        this.filemanager.setAttribute('hidden', true);
      });

      this.filemanager.addEventListener("selectfile", e => {
        this.changeValue(e.detail.url);
        this.changePreview(e.detail.thumbnail)
        this.filemanager.setAttribute('hidden', true);
      });

      this.changeValue(this.fileInput.value);
      this.changePreview(this.fileInput.value ? `/files/${this.fileInput.value}` : null);
    }
  }

  changeValue(value) {
    this.fileInput.value = value;
  }

  changePreview(value) {
    if (value) {
      this.querySelector('.image--preview').innerHTML = `<a href="${value}" class="editor__image--preview-image" style="background-image: url('${value}');background-size: contain"><span class="sr-only">Preview the image</span></a>`
    } else {
      this.querySelector('.image--preview').innerHTML = '';
    }
  }

  disconnectedCallback() {
  }
}

window.customElements.define('image-manager', ImageManager);
