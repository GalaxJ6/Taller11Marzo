from flask import Flask, request, jsonify
from flask_cors import CORS
import firebase_admin
from firebase_admin import credentials, firestore

app = Flask(__name__)
CORS(app)

# Inicializar Firebase
try:
    cred = credentials.Certificate('taller11marzo-f06eb-firebase-adminsdk-fbsvc-e080ed7d0b.json')
    firebase_admin.initialize_app(cred)
    db = firestore.client()
    print("Firebase inicializado correctamente")
except Exception as e:
    print(f"Error al inicializar Firebase: {e}")
    db = None

@app.route('/productos', methods=['GET'])
def consultar_productos():
    productos_ref = db.collection('productos')
    productos = [doc.to_dict() for doc in productos_ref.stream()]
    return jsonify(productos)

@app.route('/productos', methods=['POST'])
def registrar_producto():
    data = request.json
    db.collection('productos').add(data)
    return jsonify({'message': 'Producto registrado'}), 201

@app.route('/productos/<id>/stock', methods=['GET'])
def verificar_stock(id):
    producto = db.collection('productos').document(id).get()
    if producto.exists:
        return jsonify({'stock': producto.to_dict().get('stock', 0)})
    else:
        return jsonify({'error': 'Producto no encontrado'}), 404

@app.route('/productos/<id>/inventario', methods=['PUT'])
def actualizar_inventario(id):
    data = request.json
    nuevo_stock = data.get('cantidad', 0)
    
    # Verificar que el producto exista
    producto_doc = db.collection('productos').document(id).get()
    if not producto_doc.exists:
        return jsonify({'error': 'Producto no encontrado'}), 404
    
    # Actualizar el stock al nuevo valor
    producto_ref = db.collection('productos').document(id)
    producto_ref.update({'stock': nuevo_stock})
    
    return jsonify({'message': 'Inventario actualizado', 'stock_nuevo': nuevo_stock})

if __name__ == '__main__':
    app.run(port=5000)