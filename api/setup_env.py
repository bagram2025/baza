#!/usr/bin/env python3
"""Создаёт виртуальное окружение и устанавливает зависимости для API."""

import os
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
REQUIREMENTS = ROOT / "requirements.txt"
VENV_DIR = ROOT / ".venv"


def run(cmd, **kwargs):
    print(f"$ {' '.join(cmd)}")
    subprocess.run(cmd, check=True, **kwargs)


def main():
    if not REQUIREMENTS.exists():
        print(f"Ошибка: не найден {REQUIREMENTS}")
        sys.exit(1)

    # Создаём виртуальное окружение
    if not VENV_DIR.exists():
        print(f"Создаём виртуальное окружение: {VENV_DIR}")
        run([sys.executable, "-m", "venv", str(VENV_DIR)])
    else:
        print(f"Виртуальное окружение уже существует: {VENV_DIR}")

    # Пулл pip
    if os.name == "nt":
        python = VENV_DIR / "Scripts" / "python.exe"
        pip = VENV_DIR / "Scripts" / "pip.exe"
    else:
        python = VENV_DIR / "bin" / "python"
        pip = VENV_DIR / "bin" / "pip"

    run([str(python), "-m", "pip", "install", "--upgrade", "pip"])
    run([str(pip), "install", "-r", str(REQUIREMENTS)])

    print("\nГотово!")
    print(f"Активируйте окружение:")
    if os.name == "nt":
        print(f"  {VENV_DIR}\\Scripts\\activate")
    else:
        print(f"  source {VENV_DIR}/bin/activate")
    print(f"\nЗапуск API:")
    print(f"  {python} -m uvicorn api.main:app --reload")


if __name__ == "__main__":
    main()
