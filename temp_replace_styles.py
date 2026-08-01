from pathlib import Path

path = Path('resources/views/roles/index.blade.php')
text = path.read_text(encoding='utf-8')
styles_start = text.index("@push('styles')")
styles_end = text.index("@endpush", styles_start) + len("@endpush")
new_styles = "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('css/roles.css') }}\">\n@endpush"
text = text[:styles_start] + new_styles + text[styles_end:]
script_start = text.index("@push('scripts')")
script_end = text.index("@endpush", script_start) + len("@endpush")
new_script = "@push('scripts')\n<script src=\"{{ asset('js/roles.js') }}\" defer></script>\n@endpush"
text = text[:script_start] + new_script + text[script_end:]
path.write_text(text, encoding='utf-8')
print('Updated roles blade assets')
