[![DOI](https://zenodo.org/badge/1256659125.svg)](https://doi.org/10.5281/zenodo.20541540)


# frompt (Folder Prompt Tree)

An AI-prompt-optimized directory tree generator written in native PHP. 

`frompt` is a lightweight CLI utility designed specifically for developers feeding codebase structures into Large Language Models (LLMs) like ChatGPT, Claude, or local tools. It generates formatted Markdown folder trees. Folders are indicated with '📁' emoji while the files use '📙' emoji regardless of its extension.

---

## 🚀 Key Features

* **AI-Prompt Ready:** Automatically wraps the directory tree output inside a Markdown ```txt ... ``` code block, making it ready for instant paste into an AI prompt.
* **Interactive Filtering:** Exclude heavy or sensitive directories (e.g., `.git`, `node_modules`, `vendor`) easily with the `--ignore` option.
* **Depth Management:** Control context window usage by restricting recursion depth with `--depth`.
* **Zero Dependencies:** Pure PHP implementation. No extra composer packages or system tools required.

---

## 🛠️ Installation

1. Clone or download `frompt.php` to your machine or project.
2. Make it globally executable (Linux/macOS):
 ```bash
   chmod +x frompt.php
 ```
3. _(Optional)_ Move it to your local binaries folder to run it anywhere:
 ```bash
   mv frompt.php /usr/local/bin/frompt
 ```

---

## 📖 Usage Examples

### 1. Basic Tree View

Generate a standard tree view of your current directory:
```sh
php frompt.php .
```
### 2. Limit Depth & Ignore Directories with wildcard (*) support (Recommended for AI Prompts)

Perfect for scanning the surface layer of a project while stripping away vendor folders and deep dependency trees to save prompt tokens:
```sh
php frompt.php . --ignore=node_modules,vendor,.git,*.txt --depth=2
```
### 3. Generate & Copy Instantly

Append the `--copy` flag to save the output straight to your host system clipboard. Perfect for a rapid `frompt -> Alt+Tab -> Ctrl+V` workflow:
```sh
php frompt.php . --ignore=classes,config --depth=1
```

### 4. Folder only display

Append the `--folderonly` flag to display folder structures, ignoring all files.:
```sh
php frompt.php . --folderonly
```

---

## 🖥️ Sample Output

Running a command like `php frompt.php . --depth=3 --ignore=classes` will render:
```txt
burauenbiblio-analytics/
├── 📙 README.md
├── 📁 api
│   ├── 📙 attendance_api.php
│   ├── 📙 circulation_api.php
│   ├── 📙 collection_api.php
│   └── 📙 ddc_api.php
├── 📁 assets
│   ├── 📁 css
│   │   └── 📙 style.css
│   └── 📁 js
│       ├── 📙 chart.js
│       ├── 📙 dashboard.js
│       ├── 📙 highcharts.js
│       ├── 📙 jquery-3.6.0.min.js
│       └── 📙 jquery.highchartTable.js
├── 📙 autoload.php
├── 📁 config
│   └── 📙 dbParams.php
├── 📙 frompt.php
└── 📙 index.php
```
---

## ⚙️ Argument & Options Syntax

The argument parser is completely order-agnostic, meaning you can place the folder path at the beginning, middle, or end of your flags.

| **Option**           | **Syntax Example**         | **Description**                                                         |
| -------------------- | -------------------------- | ----------------------------------------------------------------------- |
| **Target Directory** | `.` or `path/to/folder`    | The root folder you want to map. Defaults to `.` if left out.           |
| `--depth`            | `--depth=2` or `--depth 2` | Maximum folder depth recursion level.                                   |
| `--ignore`           | `--ignore=dir1,file2,*.ext`| Comma-separated list (no spaces) of items to hide from the output tree. Supports (*) wildcard for chosen extension i.e. *.jpg, *.md|
| `--copy`             | `--copy`                   | Triggers the multi-platform clipboard mechanism.                        |
| `--help`, `-h`       | `--help`                   | Displays usage instructions.                                            |
| `--folderonly`       | `--folderonly`             | Displays the structural backbone of the path.                           |

---

## 🐳 Docker and Remote Environments

Unlike standard scripts that rely purely on localized binary tools like `xclip` or the Windows `clip` utility (which notoriously break Unicode symbols and emojis), `frompt` actively sniffs the environment:

1. **Windows Host:** Seamlessly handles UTF-8 formatting using an internal PowerShell engine.
    
2. **Linux/macOS Host:** Utilizes native pipes securely.
    
3. **Headless/Docker:** (works fine except for the --copy command, i just copy the generated tree view manually).

---

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## ❤️ A Note

This project was created to help developers improve their AI related workflows.

---

*frompt (Folder Prompt Tree)

Copyright (c) 2026 Ferdinand Tumulak

License: MIT*
