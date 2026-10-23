<?php
declare(strict_types=1);

$operations = [
	'add' => ['label' => 'Add', 'symbol' => '+'],
	'subtract' => ['label' => 'Subtract', 'symbol' => '-'],
	'multiply' => ['label' => 'Multiply', 'symbol' => '×'],
	'divide' => ['label' => 'Divide', 'symbol' => '÷'],
];

$firstInput = '';
$secondInput = '';
$operation = 'add';
$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$firstInput = trim((string) ($_POST['first'] ?? ''));
	$secondInput = trim((string) ($_POST['second'] ?? ''));
	$operation = (string) ($_POST['operation'] ?? 'add');

	if (!isset($operations[$operation])) {
		$error = 'Choose a valid operation.';
	} elseif ($firstInput === '' || $secondInput === '' || !is_numeric($firstInput) || !is_numeric($secondInput)) {
		$error = 'Enter a valid number in both fields.';
	} else {
		$first = (float) $firstInput;
		$second = (float) $secondInput;

		if ($operation === 'divide' && $second === 0.0) {
			$error = 'You can’t divide by zero.';
		} else {
			$result = match ($operation) {
				'add' => $first + $second,
				'subtract' => $first - $second,
				'multiply' => $first * $second,
				'divide' => $first / $second,
			};
		}
	}
}

function escape(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Simple Calculator</title>
	<style>
		:root {
			color-scheme: light;
			--ink: #202b28;
			--muted: #66736d;
			--paper: #f4f5ef;
			--surface: #fffefa;
			--line: #dce2d8;
			--green: #176b50;
			--green-dark: #10543e;
			--red: #a23e32;
			--lime: #d8ed91;
		}

		* { box-sizing: border-box; }

		body {
			min-height: 100vh;
			margin: 0;
			display: grid;
			place-items: center;
			padding: 24px;
			color: var(--ink);
			background-color: var(--paper);
			background-image: radial-gradient(#dce2d8 0.8px, transparent 0.8px);
			background-size: 18px 18px;
			font-family: ui-rounded, "Trebuchet MS", sans-serif;
		}

		main {
			width: min(100%, 440px);
			padding: 32px;
			border: 1px solid var(--line);
			border-top: 5px solid var(--green);
			border-radius: 8px;
			background: var(--surface);
			box-shadow: 0 16px 48px #253c2b12;
			animation: arrive 360ms ease-out both;
		}

		.eyebrow {
			margin: 0 0 8px;
			color: var(--green);
			font-size: 12px;
			font-weight: 700;
			letter-spacing: 1.2px;
			text-transform: uppercase;
		}

		h1 {
			margin: 0;
			font-family: Georgia, "Times New Roman", serif;
			font-size: 36px;
			font-weight: 500;
			line-height: 1.1;
		}

		.intro {
			margin: 10px 0 28px;
			color: var(--muted);
			font-size: 15px;
		}

		form { display: grid; gap: 18px; }

		.field { display: grid; gap: 7px; }

		label {
			font-size: 13px;
			font-weight: 700;
		}

		input, select {
			width: 100%;
			min-height: 48px;
			padding: 0 13px;
			border: 1px solid var(--line);
			border-radius: 5px;
			color: var(--ink);
			background: #fff;
			font: inherit;
			font-size: 16px;
		}

		input:focus, select:focus {
			outline: 3px solid #176b5033;
			border-color: var(--green);
		}

		button {
			min-height: 50px;
			margin-top: 3px;
			border: 0;
			border-radius: 5px;
			color: white;
			background: var(--green);
			font: inherit;
			font-size: 16px;
			font-weight: 700;
			cursor: pointer;
			transition: background 140ms ease, transform 140ms ease;
		}

		button:hover { background: var(--green-dark); }
		button:active { transform: translateY(1px); }
		button:focus-visible { outline: 3px solid var(--lime); outline-offset: 3px; }

		.message {
			margin: 20px 0 0;
			padding: 13px 15px;
			border-radius: 5px;
			font-size: 14px;
		}

		.error { color: var(--red); background: #faeeea; }

		.answer {
			display: grid;
			gap: 5px;
			border-left: 3px solid var(--green);
			color: var(--muted);
			background: #edf3e9;
		}

		.answer strong {
			color: var(--ink);
			font-family: Georgia, "Times New Roman", serif;
			font-size: 30px;
			font-weight: 500;
			overflow-wrap: anywhere;
		}

		@keyframes arrive {
			from { opacity: 0; transform: translateY(8px); }
			to { opacity: 1; transform: translateY(0); }
		}

		@media (max-width: 480px) {
			body { padding: 16px; }
			main { padding: 25px 21px; }
		}

		@media (prefers-reduced-motion: reduce) {
			*, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
		}
	</style>
</head>
<body>
	<main>
		<p class="eyebrow">A little everyday math</p>
		<h1>Calculator</h1>
		<p class="intro">Simple sums, no fuss.</p>

		<form method="post">
			<div class="field">
				<label for="first">First number</label>
				<input id="first" name="first" type="number" step="any" inputmode="decimal" value="<?= escape($firstInput) ?>" required>
			</div>

			<div class="field">
				<label for="operation">Operation</label>
				<select id="operation" name="operation">
					<?php foreach ($operations as $value => $details): ?>
						<option value="<?= escape($value) ?>" <?= $operation === $value ? 'selected' : '' ?>><?= escape($details['label']) ?> (<?= escape($details['symbol']) ?>)</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="field">
				<label for="second">Second number</label>
				<input id="second" name="second" type="number" step="any" inputmode="decimal" value="<?= escape($secondInput) ?>" required>
			</div>

			<button type="submit">Calculate</button>
		</form>

		<?php if ($error !== null): ?>
			<p class="message error" role="alert"><?= escape($error) ?></p>
		<?php elseif ($result !== null): ?>
			<p class="message answer" role="status">
				<span>Result</span>
				<strong><?= escape((string) $result) ?></strong>
			</p>
		<?php endif; ?>
	</main>
</body>
</html>
