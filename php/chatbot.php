<?php

require 'vendor/autoload.php';
use GuzzleHttp\Client;

class ChatBot
{
    private $authorization;
    private $endpoint;
    private $assistantId;
    private $threadId;

    public function __construct()
    {
        // For security purposes I've removed the API key that I have used for the testing and development of the chatbot
        $this->authorization = 'YOUR_API_KEY'; // Replace with your actual OpenAI API key
        $this->endpoint = 'https://api.openai.com/v1';
        $this->assistantId = $this->createAssistant();
        $this->threadId = $this->createThread();
    }

    private function createAssistant(): string
    {
        $client = new Client();
        $response = $client->post("{$this->endpoint}/assistants", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authorization,
                'OpenAI-Beta' => 'assistants=v2'
            ],
            'json' => [
                'instructions' => 'You are a helpful assistant.',
                'name' => 'Support Assistant',
                'model' => 'gpt-3.5-turbo'
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);
        return $responseData['id'];
    }

    private function createThread(): string
    {
        $client = new Client();
        $response = $client->post("{$this->endpoint}/threads", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authorization,
                'OpenAI-Beta' => 'assistants=v2'
            ],
            'json' => []
        ]);

        $responseData = json_decode($response->getBody(), true);
        return $responseData['id'];
    }

    public function sendMessage(string $message): string
    {
        $client = new Client();

        // Add the user message to the thread
        $client->post("{$this->endpoint}/threads/{$this->threadId}/messages", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authorization,
                'OpenAI-Beta' => 'assistants=v2'
            ],
            'json' => [
                'role' => 'user',
                'content' => $message
            ],
        ]);

        // Create a run to get the response from the assistant
        $response = $client->post("{$this->endpoint}/threads/{$this->threadId}/runs", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authorization,
                'OpenAI-Beta' => 'assistants=v2'
            ],
            'json' => [
                'assistant_id' => $this->assistantId
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);
        $runId = $responseData['id'];

        // Poll for the run's completion
        while (true) {
            $runResponse = $client->get("{$this->endpoint}/threads/{$this->threadId}/runs/{$runId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->authorization,
                    'OpenAI-Beta' => 'assistants=v2'
                ],
            ]);

            $runData = json_decode($runResponse->getBody(), true);

            if ($runData['status'] === 'completed') break;
            sleep(1); // Wait for a second before checking again
        }

        // Fetch the assistant's response
        $messagesResponse = $client->get("{$this->endpoint}/threads/{$this->threadId}/messages", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authorization,
                'OpenAI-Beta' => 'assistants=v2'
            ],
        ]);

        $messagesData = json_decode($messagesResponse->getBody(), true);
        foreach ($messagesData as $message) {
            if ($message['role'] === 'assistant') {
                return $message['content'];
            }
        }

        return 'Sorry, I could not generate a response.';
    }
}
?>
