import os
import argparse
from llama_index.core import VectorStoreIndex, SimpleDirectoryReader, StorageContext
from llama_index.core import Settings
from llama_index.llms.ollama import Ollama
from llama_index.embeddings.ollama import OllamaEmbedding

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--pdf_path', required=True)
    parser.add_argument('--vector_store', required=True)
    parser.add_argument('--question', required=False)
    args = parser.parse_args()

    # Konfigurasi Ollama
    Settings.llm = Ollama(model="llama3", request_timeout=60.0)
    Settings.embed_model = OllamaEmbedding(model_name="llama3")

    # Proses PDF
    documents = SimpleDirectoryReader(input_files=[args.pdf_path]).load_data()
    
    if args.question:
        # Query mode
        index = VectorStoreIndex.from_documents(
            documents,
            storage_context=StorageContext.from_defaults(persist_dir=args.vector_store)
        )
        
        query_engine = index.as_query_engine()
        response = query_engine.query(args.question)
        print(json.dumps({
            "answer": str(response),
            "sources": [node.node.metadata for node in response.source_nodes]
        }))
    else:
        # Indexing mode
        index = VectorStoreIndex.from_documents(documents)
        index.storage_context.persist(persist_dir=args.vector_store)
        print(json.dumps({"status": "success"}))

if __name__ == "__main__":
    main()