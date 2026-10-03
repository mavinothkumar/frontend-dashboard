<?php

namespace FED\Contracts\Ai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface AiDriverInterface
 *
 * Contract for AI Assistant and Copilot integrations (Gemini, OpenAI, Anthropic, Custom LLMs).
 */
interface AiDriverInterface {

	/**
	 * Unique driver identifier (e.g. 'gemini', 'openai').
	 *
	 * @return string
	 */
	public function getId(): string;

	/**
	 * User-friendly driver name.
	 *
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Generate text content from a prompt.
	 *
	 * @param string $prompt
	 * @param array  $options
	 * @return string
	 */
	public function generateText( string $prompt, array $options = array() ): string;

	/**
	 * Generate a professional user bio suggestion.
	 *
	 * @param string $profession
	 * @param array  $skills
	 * @return string
	 */
	public function suggestBio( string $profession, array $skills = array() ): string;

	/**
	 * Check if content violates moderation standards.
	 *
	 * @param string $text
	 * @return bool True if content is safe, False if toxic/flagged.
	 */
	public function moderateContent( string $text ): bool;
}
