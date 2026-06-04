# Technical Note: frompt — An Optimized, Low-Footprint Directory Tree Metadata Generator for LLM Context Window Ingestion

**Author:** Ferdinand Tumulak
**Repository:** https://github.com/dtr-kalfer/frompt


## Abstract

As Generative Artificial Intelligence (AI) and Large Language Models (LLMs) become deeply integrated into software engineering research and academic workflows, providing models with accurate repository context remains a critical bottleneck. Standard directory mapping utilities frequently rely on heavy, non-portable host dependencies or generate outputs that break UTF-8 formatting when transferred across isolated network boundaries (e.g., Docker containers, remote SSH sessions). This paper introduces `frompt` (Folder Prompt Tree), a native, zero-dependency PHP Command Line Interface (CLI) utility optimized explicitly for LLM context ingestion. `frompt` automatically handles terminal-agnostic clipboard synchronizations (including OSC 52 fallbacks) and standardizes code block encapsulation to maximize prompt structural integrity while minimizing token consumption.

## 1. Introduction and Rationale

When using LLMs for codebase analysis, automated code generation, or structural refactoring, researchers must provide the AI with a coherent map of the project's layout. While standard operating system utilities like `tree` exist, they present three primary limitations in modern, containerized research workflows:

1. **Host-Container Isolation:** Tools running inside headless Docker containers or remote compute nodes cannot natively access the researcher's local host clipboard.
    
2. **Character Corruption:** Traditional clipboard piping utilities (such as the legacy Windows `clip.exe`) often strips or breaks multi-byte UTF-8 characters, corrupting visual tree connectors (`├──`, `└──`) and iconography used by LLMs to distinguish files from directories.
    
3. **Context Window Inefficiencies:** Standard tools lack granular, order-agnostic flag exclusions, causing deep dependency trees (e.g., `node_modules`, `vendor`) to flood the LLM context window with irrelevant tokens.
    

`frompt` addresses these limitations by providing a single-file, highly portable PHP engine that bridges container boundaries and formats file hierarchies explicitly for markdown-based prompt architectures.

## 2. Methodology and Implementation

### 2.1. Recursive Hierarchy Extraction and Token Optimization

`frompt` utilizes an optimized recursive traversal algorithm implemented in native PHP. To prevent token bloat inside the LLM context window, the utility separates flag parsing from file system scanning, executing filtering _before_ generating tree branches.

```
[Target Directory] ──> [Filter Layer (Ignore List)] ──> [Depth Controller] ──> [Markdown Format Engine]
```

- **Depth Control:** A strict recursion counter matches the current folder depth against a user-defined threshold (`--depth=N`), instantly halting sub-tree execution once the threshold is met.
    
- **Exclusion Filtering:** Array-level diffing discards specified directories (e.g., `.git`, configuration files) early in the execution cycle, minimizing memory overhead and ensuring only high-value structural context is compiled.
    

### 2.2. Markdown Encapsulation Sequence

To eliminate manual formatting steps for the user, `frompt` natively wraps its terminal output in a clean Markdown text code block structure:

Plaintext

````
```txt
[Directory Name]/
├── 📁 directory_name
└── 📙 file_name.ext
```
````

This specific structure ensures that when the payload is ingested by an LLM, the model instantly interprets the block as pre-formatted literal text metadata rather than executable instruction prose, preserving spatial relationships.

### 2.3. Multi-Environment Clipboard Synchronization Pipeline

The core architectural innovation of `frompt` is its cross-platform clipboard pipeline, designed to automatically bypass container isolation without requiring external network bridges or host-side daemon installations:

```
                   ┌───> Windows Host ───> Native UTF-8 PowerShell Pipeline
                   │
[frompt.php Output]├───> Linux / macOS ───> Native GUI Pipes (xclip / pbcopy)
                   │
                   └───> Headless Docker ───> Base64 OSC 52 ANSI Escape Stream ───> Local Host Clipboard
```

1. **Native Host Windows Execution:** It intercepts the environment and uses an internal PowerShell pipeline enforcing strict `[System.Text.Encoding]::UTF8` streams, neutralizing Windows ANSI conversion bugs.
    
2. **Headless Linux / Docker Fallback via OSC 52:** When standard GUI clipboard systems are absent, `frompt` converts the entire markdown tree string into a Base64-encoded payload. It then emits an Operating System Command 52 (`OSC 52`) ANSI escape sequence (`\e]52;c;[BASE64]\a`). Modern terminal emulators running the container stream intercept this sequence, decode it locally, and write the contents directly into the researcher's local host system clipboard.
    

## 3. Use Case and Operational Impact

In an experimental setting evaluating codebase structure ingestion, a typical multidisciplinary repository containing deep operational modules was parsed.

By running:

Bash

```
php frompt.php . --ignore=classes,config --depth=1 --copy
```

The system condensed a sprawling folder layout into a highly compact, structured metadata block, omitting thousands of tokens of redundant dependency files while automatically placing the formatted block into the user's local clipboard with zero terminal leakage.

## 4. Conclusion and Availability

`frompt` provides a minimalist, robust, and highly secure bridge for moving codebase architecture context into generative AI pipelines. By solving the multi-byte character corruption and container clipboard isolation barriers via OSC 52, it optimizes data-gathering efficiency for software engineers and AI prompt researchers alike.

The source code is open-source, licensed under the MIT License.