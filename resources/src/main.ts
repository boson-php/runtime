import type {BosonDataApi} from "./data/BosonDataApi";

import type IdGeneratorInterface from "./id-generator/IdGeneratorInterface";
import type {IdType} from "./id-generator/IdGeneratorInterface";
import IdGeneratorFactory from "./id-generator/IdGeneratorFactory";

import BosonRpc from "./rpc/BosonRpc";

import type {TransportInterface} from "./transport/TransportInterface";
import TransportFactory from "./transport/TransportFactory";

declare const window: {
    boson: {
        io?: TransportInterface,
        ids?: IdGeneratorInterface<IdType>,
        rpc?: BosonRpc<IdType>,
        data?: BosonDataApi<IdType>,
    },
};

/**
 * Prepare public accessor instance.
 */
window.boson ||= {};


/**
 * Initialize IO subsystem
 */
try {
    window.boson.io ||= TransportFactory.createFromGlobals();
} catch (e) {
    console.error('Failed to initialize IPC subsystem', e);
}


/**
 * Initialize IDs generator subsystem
 */
try {
    window.boson.ids ||= IdGeneratorFactory.createFromGlobals();
} catch (e) {
    console.error('Failed to initialize ID generator subsystem', e);
}


/**
 * Initialize RPC subsystem
 */
try {
    if (!window.boson.io || !window.boson.ids) {
        throw new Error('Could not initialize RPC: Requires IPC and ID generator subsystems');
    }

    window.boson.rpc ||= new BosonRpc(window.boson.io, window.boson.ids);
} catch (e) {
    console.error('Failed to initialize RPC subsystem', e);
}
